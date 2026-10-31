<?php
namespace app\components\payments;

use yii\web\Request;
use yii\helpers\Url;

class VoletGateway implements PaymentGatewayInterface
{
    private function cfg(string $key): string
    {
        return trim((string)\app\components\payments\AppConfig::get("payment.volet.{$key}", ''));
    }

    private function isTest(): bool
    {
        return $this->cfg('test') === '1';
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $apiId  = $this->cfg('api_id');
        $apiKey = $this->cfg('api_key');

        if ($apiId === '' || $apiKey === '') {
            throw new \RuntimeException('Volet credentials not configured.');
        }

        $data = [
            'amount'      => $amount,
            'currency'    => $currency,
            'order_id'    => $orderId,
            'description' => $description,
            'success_url' => Url::to(['/cabinet/deposit/success', 'provider' => 'volet', 'orderId' => $orderId], true),
            'fail_url'    => Url::to(['/cabinet/deposit/fail', 'provider' => 'volet', 'orderId' => $orderId], true),
            'api_id'      => $apiId,
            'sign'        => hash('sha256', $apiId . $amount . $currency . $orderId . $apiKey),
        ];

        // Подмена URL для тестового режима (УТОЧНИТЕ В ДОКУМЕНТАЦИИ VOLet)
        $url = $this->isTest() ? 'https://sandbox.volet.com/invoice' : 'https://volet.com/invoice';
        
        return $url . '?' . http_build_query($data);
    }

    public function handleCallback(Request $request): array
    {
        $post = $request->post();
        $sign = hash('sha256', ($post['invoice_id'] ?? '') . ($post['amount'] ?? '') . ($post['status'] ?? '') . $this->cfg('api_key'));
        if ($sign !== ($post['sign'] ?? '')) {
            return ['ok' => false, 'rawResponse' => 'Invalid signature'];
        }

        return [
            'ok'          => true,
            'orderId'     => $post['order_id'] ?? '',
            'amount'      => (float)($post['amount'] ?? 0),
            'externalId'  => $post['invoice_id'] ?? '',
            'rawResponse' => 'OK',
        ];
    }

    public function successUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/success', 'provider' => 'volet', 'orderId' => $orderId], true);
    }

    public function failUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/fail', 'provider' => 'volet', 'orderId' => $orderId], true);
    }

    public function validateCredentials(): array
    {
        $apiId  = $this->cfg('api_id');
        $apiKey = $this->cfg('api_key');

        if ($apiId === '' || $apiKey === '') {
            return ['ok' => false, 'message' => 'API ID или API Key не заполнены'];
        }

        $url = $this->isTest() ? 'https://sandbox.volet.com/api/v1/balance' : 'https://api.volet.com/api/v1/balance';
        $sign = hash('sha256', $apiId . $apiKey);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Api-Id: ' . $apiId,
                'Api-Sign: ' . $sign,
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
        $msg = $data['message'] ?? 'Ошибка API (HTTP ' . $code . ')';
        return ['ok' => false, 'message' => 'Volet: ' . $msg];
    }
}