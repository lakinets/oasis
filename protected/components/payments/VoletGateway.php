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

        /*  Замените на реальный endpoint Volet после регистрации мерчанта  */
        return 'https://volet.com/invoice?' . http_build_query($data);
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
}