<?php
namespace app\components\payments;

use yii\web\Request;
use yii\helpers\Url;

class InterkassaGateway implements PaymentGatewayInterface
{
    private string $apiBase = 'https://sci.interkassa.com';

    private function cfg(string $key): string
    {
        return trim((string)\app\components\payments\AppConfig::get("payment.interkassa.{$key}", ''));
    }

    private function isTest(): bool
    {
        return $this->cfg('test') === '1';
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $checkoutId = $this->cfg('checkout_id');
        $secretKey  = $this->isTest() ? $this->cfg('test_key') : $this->cfg('secret_key');

        if ($checkoutId === '' || $secretKey === '') {
            throw new \RuntimeException('Interkassa credentials not configured.');
        }

        // При тесте Interkassa требует валюту XTS
        if ($this->isTest()) {
            $currency = 'XTS';
        }

        $params = [
            'ik_co_id'     => $checkoutId,
            'ik_pm_no'     => $orderId,
            'ik_am'        => number_format($amount, 2, '.', ''),
            'ik_cur'       => $currency,
            'ik_desc'      => $description,
            'ik_ia_u'      => Url::to(['/cabinet/deposit/interkassa-callback'], true),
            'ik_suc_u'     => $this->successUrl($orderId),
            'ik_fal_u'     => $this->failUrl($orderId),
            'ik_exp'       => 3600,
        ];

        ksort($params, SORT_STRING);
        $signStr = implode(':', array_values($params)) . ':' . $secretKey;
        $params['ik_sign'] = base64_encode(hash('sha256', $signStr, true));

        return $this->apiBase . '/?' . http_build_query($params);
    }

    public function handleCallback(Request $request): array
    {
        $data = $request->post();
        $secretKey = $this->isTest() ? $this->cfg('test_key') : $this->cfg('secret_key');
        
        if ($secretKey === '') {
            return ['ok' => false, 'rawResponse' => 'Secret not set'];
        }

        $sign = $data['ik_sign'] ?? '';
        unset($data['ik_sign']);
        ksort($data, SORT_STRING);
        $signStr = implode(':', array_values($data)) . ':' . $secretKey;
        $calcSign = base64_encode(hash('sha256', $signStr, true));

        if (!hash_equals($calcSign, $sign)) {
            return ['ok' => false, 'rawResponse' => 'Bad signature'];
        }

        $status = strtolower($data['ik_inv_st'] ?? '');
        $isPaid = ($status === 'success');

        return [
            'ok'          => $isPaid,
            'orderId'     => (string)($data['ik_pm_no'] ?? ''),
            'amount'      => (float)($data['ik_am'] ?? 0),
            'externalId'  => (string)($data['ik_trn_id'] ?? ''),
            'rawResponse' => 'OK',
        ];
    }

    public function successUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/success', 'provider' => 'interkassa', 'orderId' => $orderId], true);
    }

    public function failUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/fail', 'provider' => 'interkassa', 'orderId' => $orderId], true);
    }

    public function validateCredentials(): array
    {
        $checkoutId = $this->cfg('checkout_id');
        $secretKey  = $this->isTest() ? $this->cfg('test_key') : $this->cfg('secret_key');

        if ($checkoutId === '' || $secretKey === '') {
            return ['ok' => false, 'message' => 'Checkout ID или Secret Key не заполнены'];
        }

        $url = 'https://api.interkassa.com/v1/payment/';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($checkoutId . ':' . $secretKey),
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

        if ($code === 401) {
            return ['ok' => false, 'message' => 'Interkassa: Неверные ключи (401 Unauthorized)'];
        }

        $data = json_decode($resp, true);
        $msg = $data['message'] ?? 'Ошибка API (HTTP ' . $code . ')';
        return ['ok' => false, 'message' => 'Interkassa: ' . $msg];
    }
}