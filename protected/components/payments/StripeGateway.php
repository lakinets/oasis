<?php
namespace app\components\payments;

use Yii;
use yii\web\Request;
use yii\helpers\Url;

/**
 * Stripe — глобальный приём карт (Visa, Mastercard, Amex), Apple Pay, Google Pay.
 */
class StripeGateway implements PaymentGatewayInterface
{
    private const API_BASE          = 'https://api.stripe.com/v1';
    private const MIN_AMOUNT_USD    = 0.50;
    private const WEBHOOK_TOLERANCE = 300; // сек, защита от replay

    private function cfg(string $key, string $default = ''): string
    {
        return trim((string)AppConfig::get("stripe.{$key}", $default));
    }

    private function isTest(): bool
    {
        return AppConfig::get('stripe.test', '1') === '1';
    }

    private function secretKey(): string
    {
        return $this->cfg($this->isTest() ? 'secret_key_test' : 'secret_key_live');
    }

    private function webhookSecret(): string
    {
        return $this->cfg($this->isTest() ? 'webhook_secret_test' : 'webhook_secret_live');
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $secretKey = $this->secretKey();
        if ($secretKey === '') {
            throw new \RuntimeException(
                'Stripe is not configured: missing ' .
                ($this->isTest() ? 'test' : 'live') . ' secret key.'
            );
        }

        $stripeCurrency = strtolower($this->cfg('currency', 'usd'));
        $rate           = (float)$this->cfg('exchange_rate', '0.011');

        // Конвертация в валюту Stripe (amount приходит в RUB из DepositController)
        $convertedAmount = (strtolower($currency) === $stripeCurrency)
            ? $amount
            : $amount * $rate;

        $unitAmount = (int)round($convertedAmount * 100); // в минимальных единицах (центы)
        $minAmount  = (int)round(self::MIN_AMOUNT_USD * 100);

        if ($unitAmount < $minAmount) {
            throw new \RuntimeException(sprintf(
                'Stripe: сумма слишком мала. Минимум $%.2f, получилось $%.2f. Пополните на большую сумму.',
                self::MIN_AMOUNT_USD,
                $unitAmount / 100
            ));
        }

        $params = [
            'mode'                                        => 'payment',
            'success_url'                                 => $this->successUrl($orderId),
            'cancel_url'                                  => $this->failUrl($orderId),
            'client_reference_id'                         => $orderId,
            'line_items[0][price_data][currency]'         => $stripeCurrency,
            'line_items[0][price_data][unit_amount]'      => $unitAmount,
            'line_items[0][price_data][product_data][name]' => $description,
            'line_items[0][quantity]'                     => 1,
            'metadata[order_id]'                          => $orderId,
            'metadata[login]'                             => $login,
        ];

        $response = $this->httpPost(self::API_BASE . '/checkout/sessions', $params, $secretKey);

        if (!is_array($response) || empty($response['url'])) {
            Yii::error(['Stripe create session failed', 'resp' => $response], __METHOD__);
            
            // ИЗВЛЕКАЕМ ТОЧНУЮ ОШИБКУ ИЗ ОТВЕТА STRIPE
            $exactError = $response['error']['message'] ?? 'Неизвестная ошибка (проверьте логи)';
            throw new \RuntimeException('Stripe Error: ' . $exactError);
        }
        return $response['url'];
    }

    public function handleCallback(Request $request): array
    {
        $rawBody = $request->getRawBody() ?: '';
        $sigHeader = (string)$request->headers->get('Stripe-Signature', '');

        if ($sigHeader === '' || $rawBody === '') {
            return ['ok' => false, 'rawResponse' => 'bad request'];
        }

        $webhookSecret = $this->webhookSecret();
        if ($webhookSecret === '') {
            return ['ok' => false, 'rawResponse' => 'webhook secret not configured'];
        }

        // Формат заголовка: t=timestamp,v1=sig1,v1=sig2,...
        $parts = [];
        foreach (explode(',', $sigHeader) as $item) {
            $kv = explode('=', trim($item), 2);
            if (count($kv) === 2) {
                $parts[$kv[0]][] = $kv[1];
            }
        }

        $timestamp  = $parts['t'][0] ?? '';
        $signatures = $parts['v1'] ?? [];

        if ($timestamp === '' || empty($signatures)) {
            return ['ok' => false, 'rawResponse' => 'bad signature header'];
        }

        if (abs(time() - (int)$timestamp) > self::WEBHOOK_TOLERANCE) {
            return ['ok' => false, 'rawResponse' => 'timestamp too old'];
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $webhookSecret);
        $valid    = false;
        foreach ($signatures as $sig) {
            if (hash_equals($expected, $sig)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            Yii::warning(['Stripe bad signature', 'expected' => $expected], __METHOD__);
            return ['ok' => false, 'rawResponse' => 'bad signature'];
        }

        $event     = json_decode($rawBody, true) ?: [];
        $eventType = $event['type'] ?? '';

        if ($eventType !== 'checkout.session.completed') {
            return ['ok' => true, 'orderId' => '', 'amount' => 0.0, 'externalId' => null, 'rawResponse' => 'ignored'];
        }

        $session = $event['data']['object'] ?? [];
        $orderId = (string)($session['client_reference_id'] ?? $session['metadata']['order_id'] ?? '');

        if (($session['payment_status'] ?? '') !== 'paid') {
            return [
                'ok'          => false,
                'orderId'     => $orderId,
                'amount'      => 0.0,
                'externalId'  => (string)($session['id'] ?? ''),
                'rawResponse' => 'not paid',
            ];
        }

        $amountTotalCents = (int)($session['amount_total'] ?? 0);
        
        // ИСПРАВЛЕНИЕ: Берем ровно ту сумму, которую списал Stripe (в центах), 
        // и переводим в единицы. Без деления на курс.
        $amount = $amountTotalCents / 100;

        return [
            'ok'          => true,
            'orderId'     => $orderId,
            'amount'      => $amount,
            'externalId'  => (string)($session['id'] ?? ''),
            'rawResponse' => 'OK',
        ];
    }

    public function successUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/success', 'provider' => 'stripe', 'orderId' => $orderId], true);
    }

    public function failUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/fail', 'provider' => 'stripe', 'orderId' => $orderId], true);
    }

    public function validateCredentials(): array
    {
        $secretKey = $this->secretKey();
        if ($secretKey === '') {
            return ['ok' => false, 'message' => 'Ключ не заполнен'];
        }

        $ch = curl_init(self::API_BASE . '/balance');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $secretKey],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['ok' => false, 'message' => 'Ошибка сети: ' . $err];
        }

        if ($code === 200) {
            return ['ok' => true, 'message' => 'Ключи рабочие (API отвечает)'];
        }

        $data = json_decode($resp, true);
        $error = $data['error']['message'] ?? 'Ошибка API (HTTP ' . $code . ')';
        
        return ['ok' => false, 'message' => 'Stripe: ' . $error];
    }

    private function httpPost(string $url, array $params, string $secretKey): array
    {
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $secretKey,
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ];
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 20,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err || $code >= 400) {
            Yii::error(['Stripe HTTP error', 'code' => $code, 'err' => $err, 'resp' => $resp], __METHOD__);
            
            // ВОЗВРАЩАЕМ ОТВЕТ, ЧТОБЫ ВЫШЕ МЫ МОГЛИ ПРОЧИТАТЬ ТЕКСТ ОШИБКИ
            $data = json_decode($resp, true);
            return is_array($data) ? $data : ['error' => ['message' => "HTTP $code: $err"]];
        }
        $data = json_decode($resp, true);
        return is_array($data) ? $data : [];
    }
}