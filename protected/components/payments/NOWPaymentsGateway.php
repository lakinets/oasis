<?php
namespace app\components\payments;

use Yii;
use yii\web\Request;
use yii\helpers\Url;

class NOWPaymentsGateway implements PaymentGatewayInterface
{
    private string $apiBase = 'https://api.nowpayments.io/v1';
    private string $apiSandboxBase = 'https://api.sandbox.nowpayments.io/v1';

    private function getApiBase(): string
    {
        return AppConfig::get('nowpayments.test', '0') === '1' ? $this->apiSandboxBase : $this->apiBase;
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $apiKey     = (string)AppConfig::get('payment.nowpayments.api_key', '');
        $ipnSecret  = (string)AppConfig::get('payment.nowpayments.ipn_secret', '');
        $fiat       = (string)AppConfig::get('payment.nowpayments.currency', $currency ?: 'RUB');
        $multiplier = (float)AppConfig::get('payment.nowpayments.price_amount_multiplier', 1.0);

        if ($apiKey === '' || $ipnSecret === '') {
            throw new \RuntimeException('NOWPayments is not configured');
        }

        $priceAmount = round($amount * $multiplier, 2);

        $payload = [
            'price_amount'   => $priceAmount,
            'price_currency' => $fiat,
            'order_id'       => $orderId,
            'order_description' => $description,
            'ipn_callback_url'  => Url::to(['/cabinet/deposit/nowpayments-callback'], true),
            'success_url'       => $this->successUrl($orderId),
            'cancel_url'        => $this->failUrl($orderId),
        ];

        $response = $this->httpPostJson($this->getApiBase() . "/payment", $payload, [
            'x-api-key: ' . $apiKey,
        ]);

        if (!is_array($response) || empty($response['payment_url'])) {
            Yii::error(['NOWPayments create payment failed', 'resp' => $response], __METHOD__);
            throw new \RuntimeException('NOWPayments error: cannot create payment');
        }
        return $response['payment_url'];
    }

    public function handleCallback(Request $request): array
    {
        $rawBody = file_get_contents('php://input') ?: '';
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $headerSig = '';
        foreach ($headers as $k => $v) {
            if (strcasecmp($k, 'x-nowpayments-sig') === 0) {
                $headerSig = trim($v);
                break;
            }
        }

        $ipnSecret = (string)AppConfig::get('payment.nowpayments.ipn_secret', '');
        if ($ipnSecret === '') {
            return ['ok' => false, 'rawResponse' => 'IPN secret not set'];
        }

        $calcSig = hash_hmac('sha512', $rawBody, $ipnSecret);
        if (!hash_equals($calcSig, $headerSig)) {
            Yii::warning(['NOWPayments bad signature', 'expected' => $calcSig, 'got' => $headerSig, 'body' => $rawBody], __METHOD__);
            return ['ok' => false, 'rawResponse' => 'Bad signature'];
        }

        $data = json_decode($rawBody, true) ?: [];
        $status = strtolower((string)($data['payment_status'] ?? ''));
        $okStatuses = ['finished', 'confirmed', 'partially_paid'];
        $isOk = in_array($status, $okStatuses, true);

        return [
            'ok'             => $isOk,
            'orderId'        => (string)($data['order_id'] ?? ''),
            'amount'         => (float)($data['price_amount'] ?? 0),
            'externalId'     => (string)($data['payment_id'] ?? ''),
            'rawResponse'    => 'OK',
        ];
    }

    public function successUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/success', 'provider' => 'nowpayments', 'orderId' => $orderId], true);
    }

    public function failUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/fail', 'provider' => 'nowpayments', 'orderId' => $orderId], true);
    }

    public function validateCredentials(): array
    {
        $apiKey = (string)AppConfig::get('payment.nowpayments.api_key', '');
        if ($apiKey === '') {
            return ['ok' => false, 'message' => 'API Key не заполнен'];
        }

        $url = $this->getApiBase() . "/balance";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $apiKey,
                'Content-Type: application/json',
            ],
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
        $errorMsg = $data['message'] ?? 'Ошибка API (HTTP ' . $code . ')';
        return ['ok' => false, 'message' => 'NOWPayments: ' . $errorMsg];
    }

    private function httpPostJson(string $url, array $body, array $headers = []): array
    {
        $ch = curl_init($url);
        $payload = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $headers = array_merge([
            'Content-Type: application/json',
            'Accept: application/json',
        ], $headers);

        curl_setopt_array($ch, [
            CURLOPT_POST            => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_POSTFIELDS      => $payload,
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_TIMEOUT         => 20,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err || $code >= 400) {
            Yii::error(['NOWPayments HTTP error', 'code' => $code, 'err' => $err, 'resp' => $resp], __METHOD__);
            return [];
        }
        $data = json_decode($resp, true);
        return is_array($data) ? $data : [];
    }
}