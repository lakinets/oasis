<?php
namespace app\components\payments;

use Yii;
use yii\web\Request;
use yii\helpers\Url;

/**
 * Cryptomus — интеграция.
 * Проверка ключей реализована через запрос баланса.
 */
class CryptomusGateway implements PaymentGatewayInterface
{
    private string $apiBase = 'https://api.cryptomus.com/v1';

    private function cfg(string $key): string
    {
        return trim((string)AppConfig::get("payment.cryptomus.{$key}", ''));
    }

    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        throw new \RuntimeException('Cryptomus integration is pending (awaiting confirmed signing flow).');
    }

    public function handleCallback(Request $request): array
    {
        return ['ok' => false, 'rawResponse' => 'Cryptomus integration pending'];
    }

    public function successUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/success', 'provider' => 'cryptomus', 'orderId' => $orderId], true);
    }

    public function failUrl(string $orderId): string
    {
        return Url::to(['/cabinet/deposit/fail', 'provider' => 'cryptomus', 'orderId' => $orderId], true);
    }

    /**
     * Проверка ключей через API Cryptomus.
     * @return array ['ok' => bool, 'message' => string]
     */
    public function validateCredentials(): array
    {
        $merchant = $this->cfg('merchant_id'); // UUID
        $apiKey   = $this->cfg('payment_api_key');

        if ($merchant === '' || $apiKey === '') {
            return ['ok' => false, 'message' => 'Merchant ID или Payment API Key не заполнены'];
        }

        // Для запроса баланса тело может быть пустым JSON {}
        $body = '{}';
        $sign = md5(base64_encode($body) . $apiKey);

        $ch = curl_init("{$this->apiBase}/balance");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'merchant: ' . $merchant,
                'sign: ' . $sign,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => $body,
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
        
        // API Cryptomus возвращает 200 даже при ошибках, проверяем поле state/result
        if (isset($data['state']) && $data['state'] === 0) {
            $errorMsg = $data['message'] ?? 'Неизвестная ошибка';
            return ['ok' => false, 'message' => 'Cryptomus: ' . $errorMsg];
        }

        if ($code === 200 && isset($data['result'])) {
            return ['ok' => true, 'message' => 'Ключи рабочие (API отвечает)'];
        }

        return ['ok' => false, 'message' => 'Неизвестный ответ API (HTTP ' . $code . ')'];
    }
}