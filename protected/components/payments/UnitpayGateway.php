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

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $publicKey = AppConfig::get('unitpay.public_key', '');
        $projectId = AppConfig::get('unitpay.project_id', '');
        // Редирект на форму оплаты по ключу проекта (подпись для редиректа не требуется)
        $params = [
            'sum'       => number_format($amount, 2, '.', ''),
            'account'   => $orderId,            // вернётся в колбэке и свяжет платёж
            'desc'      => $description,
            'currency'  => $currency,
            'projectId' => $projectId,
            // Можно добавить customerEmail/phone если хотите
        ];
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

        // Обработка только события оплаты
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

        // На check/other — отвечаем OK, но без начисления
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
}
