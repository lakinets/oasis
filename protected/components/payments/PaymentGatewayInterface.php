<?php
namespace app\components\payments;

use yii\web\Request;

interface PaymentGatewayInterface
{
    /**
     * Возвращает URL, куда редиректим пользователя на оплату.
     */
    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string;

    /**
     * Обработчик колбэка (Result/Callback). Должен:
     *  - проверить подпись
     *  - вернуть ['ok' => true, 'orderId' => ..., 'amount' => ..., 'externalId' => ..., 'rawResponse' => stringForEcho]
     * rawResponse — это ровно то, что нужно вернуть провайдеру в HTTP-ответе (например "OK<InvId>" для Robokassa или JSON для Unitpay).
     */
    public function handleCallback(Request $request): array;

    /**
     * URLы "успех/ошибка" для редиректа клиента после оплаты (визуальные страницы).
     */
    public function successUrl(string $orderId): string;
    public function failUrl(string $orderId): string;
}
