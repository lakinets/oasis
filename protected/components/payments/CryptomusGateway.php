<?php
namespace app\components\payments;

use yii\web\Request;
use yii\helpers\Url;

/**
 * Cryptomus — каркас. Провайдер скрыт из списка до подтверждения алгоритма подписи/эндпойнтов (создание платежа, заголовки/подпись).
 */
class CryptomusGateway implements PaymentGatewayInterface
{
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
}
