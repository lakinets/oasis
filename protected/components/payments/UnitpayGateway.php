<?php
namespace app\components\payments;

use Yii;
use yii\web\Request;

class UnitpayGateway implements PaymentGatewayInterface
{
    private function buildSignature(string $method, array $params, string $secret): string
    {
        unset($params['signature'], $params['sign']);
        ksort($params);
        $values = array_values($params);
        array_unshift($values, $method);
        $toHash = implode('{up}', $values) . $secret;
        return hash('sha256', $toHash);
    }

    private function isTest(): bool
    {
        return AppConfig::get('unitpay.test', '0') === '1';
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $publicKey = AppConfig::get('unitpay.public_key', '');
        $projectId = AppConfig::get('unitpay.project_id', '');
        
        $params = [
            'sum'       => number_format($amount, 2, '.', ''),
            'account'   => $orderId,
            'desc'      => $description,
            'currency'  => $currency,
            'projectId' => $projectId,
        ];

        if ($this->isTest()) {
            $params['test'] = '1'; // Включаем тестовый режим Unitpay
        }

        return 'https://unitpay.money/pay/' . rawurlencode($publicKey) . '?' . http_build_query($params);
    }

    public function handleCallback(Request $request): array
    {
        $method = $request->get('method');
        $params = $request->get('params', []);
        if (!$method || !is_array($params)) {
            return ['ok' => false, 'rawResponse' => json_encode(['error' => ['message' => 'bad request']])];
        }

        $secret = AppConfig::get('unitpay.secret_key', '');
        $theirSign = $params['signature'] ?? ($params['sign'] ?? '');
        $mySign = $this->buildSignature($method, $params, $secret);

        if (!hash_equals($theirSign, $mySign)) {
            return ['ok' => false, 'rawResponse' => json_encode(['error' => ['message' => 'signature mismatch']])];
        }

        if ($method === 'pay') {
            $orderId = (string)($params['account'] ?? '');
            $amount  = (float)($params['orderSum'] ?? $params['sum'] ?? 0);
            $extId   = (string)($params['unitpayId'] ?? '');
            return [
                'ok'          => true,
                'orderId'     => $orderId,
                'amount'      => $amount,
                'externalId'  => $extId,
                'rawResponse' => json_encode(['result' => ['message' => 'OK']]),
            ];
        }

        return ['ok' => true, 'orderId' => (string)($params['account'] ?? ''), 'amount' => 0.0, 'externalId' => null,
            'rawResponse' => json_encode(['result' => ['message' => 'OK']])];
    }

    public function successUrl(string $orderId): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/cabinet/deposit/success', 'orderId' => $orderId, 'provider' => 'unitpay']);
    }

    public function failUrl(string $orderId): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/cabinet/deposit/fail', 'orderId' => $orderId, 'provider' => 'unitpay']);
    }

    public function validateCredentials(): array
    {
        $projectId = AppConfig::get('unitpay.project_id', '');
        $secretKey = AppConfig::get('unitpay.secret_key', '');

        if ($projectId === '' || $secretKey === '') {
            return ['ok' => false, 'message' => 'Project ID или Secret Key не заполнены'];
        }

        $url = 'https://unitpay.ru/api?' . http_build_query([
            'method'    => 'getMethodsAvailable',
            'projectId' => $projectId,
            'secretKey' => $secretKey,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['ok' => false, 'message' => 'Ошибка сети: ' . $err];
        }

        $data = json_decode($resp, true);
        if (isset($data['error'])) {
            return ['ok' => false, 'message' => 'Unitpay: ' . ($data['error']['message'] ?? 'Неизвестная ошибка')];
        }

        if ($code === 200 && isset($data['result'])) {
            return ['ok' => true, 'message' => 'Ключи рабочие (API отвечает)'];
        }

        return ['ok' => false, 'message' => 'Неизвестный ответ API (HTTP ' . $code . ')'];
    }
}