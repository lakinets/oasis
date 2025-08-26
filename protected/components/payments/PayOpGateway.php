<?php
namespace app\components\payments;

use yii\web\Request;
use yii\helpers\Url;
use yii\httpclient\Client;

/**
 * Полная рабочая интеграция PayOp.
 * Данные берутся из таблицы config:
 *  - payment.payop.enabled      (0|1)
 *  - payment.payop.public_key   (publicKey)
 *  - payment.payop.secret_key   (secretKey)
 *  - payment.payop.project_id   (projectId)
 *
 * Создание платежа: POST /v1/payments
 * Callback: POST cabinet/deposit/payop-callback
 */
class PayOpGateway implements PaymentGatewayInterface
{
    private string $apiBase = 'https://payop.com/v1';

    private function cfg(string $key): string
    {
        return trim((string)\app\components\payments\AppConfig::get("payment.payop.{$key}", ''));
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $publicKey = $this->cfg('public_key');
        $secretKey = $this->cfg('secret_key');
        $projectId = $this->cfg('project_id');

        if ($publicKey === '' || $secretKey === '' || $projectId === '') {
            throw new \RuntimeException('PayOp credentials not configured.');
        }

        $payload = [
            'publicKey'  => $publicKey,
            'amount'     => $amount,
            'currency'   => $currency,
            'order'      => [
                'id'          => $orderId,
                'description' => $description,
            ],
            'resultUrl'  => $this->successUrl($orderId),
            'failUrl'    => $this->failUrl($orderId),
        ];

        // Подпись: sha256(json_encode($data) . secretKey)
        $payload['signature'] = hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES) . $secretKey);

        $client = new Client(['transport' => 'yii\httpclient\CurlTransport']);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl("{$this->apiBase}/payments")
            ->setFormat(Client::FORMAT_JSON)
            ->setData($payload)
            ->send();

        if (!$response->isOk) {
            throw new \RuntimeException('PayOp error: ' . $response->content);
        }

        $data = $response->getData();
        if (empty($data['data']['redirectUrl'])) {
            throw new \RuntimeException('PayOp redirect URL missing');
        }

        return $data['data']['redirectUrl'];
    }

    public function handleCallback(Request $request): array
    {
        $rawBody = $request->getRawBody();
        $data    = json_decode($rawBody, true) ?: [];

        // PayOp присылает JSON, в котором есть transaction
        $tx = $data['transaction'] ?? [];

        // Статус 1 = успешно
        $isPaid = isset($tx['state']) && (int)$tx['state'] === 1;

        return [
            'ok'          => $isPaid,
            'orderId'     => (string)($tx['order']['id'] ?? ''),
            'amount'      => (float)($tx['amount'] ?? 0),
            'externalId'  => (string)($tx['id'] ?? ''),
            'rawResponse' => 'OK',
        ];
    }

    public function successUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/success', 'provider' => 'payop', 'orderId' => $orderId], true);
    }

    public function failUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/fail', 'provider' => 'payop', 'orderId' => $orderId], true);
    }
}