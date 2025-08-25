<?php
namespace app\components\payments;

use yii\web\Request;
use yii\helpers\Url;

/**
 * PayOp — каркас. Оставлен как класс для будущей доработки,
 * провайдер скрыт из списка до подтверждения алгоритма подписи/эндпойнтов.
 */
class PayOpGateway implements PaymentGatewayInterface
{
    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        throw new \RuntimeException('PayOp integration is pending (awaiting confirmed signing flow).');
    }

    public function handleCallback(Request $request): array
    {
        return ['ok' => false, 'rawResponse' => 'PayOp integration pending'];
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
