<?php
namespace app\components\payments;

use Yii;
use yii\web\Request;

class RobokassaGateway implements PaymentGatewayInterface
{
    public function buildRedirectUrl(string $orderId, string $login, float $amount, string $currency, string $description): string
    {
        $isTest = AppConfig::get('robokassa.test', '0') === '1' ? 1 : 0;
        $merchantLogin = AppConfig::get('robokassa.login', '');
        $password1     = AppConfig::get('robokassa.password', '');

        $outSum = number_format($amount, 2, '.', '');
        // Подпись для редиректа (Password#1). Включаем кастомный параметр Shp_login.
        $signature = md5("$merchantLogin:$outSum:$orderId:$password1:Shp_login=$login");

        $params = [
            'MerchantLogin'   => $merchantLogin,
            'OutSum'          => $outSum,
            'InvId'           => $orderId,
            'Description'     => $description,
            'SignatureValue'  => $signature,
            'Shp_login'       => $login,
            'IsTest'          => $isTest,
            'Culture'         => 'ru',
        ];

        return 'https://auth.robokassa.ru/Merchant/Index.aspx?' . http_build_query($params);
    }

    public function handleCallback(Request $request): array
    {
        // ResultURL: OutSum, InvId, SignatureValue, а также наши Shp_*
        $outSum   = $request->post('OutSum', $request->get('OutSum'));
        $invId    = $request->post('InvId',  $request->get('InvId'));
        $sig      = $request->post('SignatureValue', $request->get('SignatureValue'));
        $login    = $request->post('Shp_login', $request->get('Shp_login'));

        if (!$outSum || !$invId || !$sig || !$login) {
            return ['ok' => false, 'rawResponse' => 'bad request'];
        }

        $password2 = AppConfig::get('robokassa.password2', '');
        // Подпись для ResultURL (Password#2) + Shp_* в алфавитном порядке
        $mySig = md5("$outSum:$invId:$password2:Shp_login=$login");

        if (strcasecmp($sig, $mySig) !== 0) {
            return ['ok' => false, 'rawResponse' => 'signature mismatch'];
        }

        return [
            'ok'          => true,
            'orderId'     => (string)$invId,
            'amount'      => (float)$outSum,
            'externalId'  => null,
            'rawResponse' => "OK$invId", // обязательно вернуть OK<InvId>
        ];
    }

    public function successUrl(string $orderId): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/cabinet/deposit/success', 'orderId' => $orderId, 'provider' => 'robokassa']);
    }

    public function failUrl(string $orderId): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/cabinet/deposit/fail', 'orderId' => $orderId, 'provider' => 'robokassa']);
    }

    /**
     * Проверка ключей Robokassa.
     * @return array ['ok' => bool, 'message' => string]
     */
    public function validateCredentials(): array
    {
        $merchantLogin = AppConfig::get('robokassa.login', '');
        $password2     = AppConfig::get('robokassa.password2', '');

        if ($merchantLogin === '' || $password2 === '') {
            return ['ok' => false, 'message' => 'Логин или Пароль2 не заполнены'];
        }

        // Используем OpStateExt для проверки статуса несуществующего заказа.
        // Если подпись неверна, сервер вернёт ошибку.
        $testInvId = 999999999; // Заведомо несуществующий ID
        $signature = md5("{$merchantLogin}:{$testInvId}:{$password2}");

        $url = 'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpStateExt?' . http_build_query([
            'MerchantLogin' => $merchantLogin,
            'InvoiceID'     => $testInvId,
            'Signature'     => $signature,
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

        // Если подпись верна, сервер вернёт XML с результатом (даже если заказ не найден).
        // Если подпись неверна, ответ будет содержать ошибку.
        if ($code === 200) {
            // Проверяем, нет ли в ответе явной ошибки подписи.
            if (stripos($resp, 'signature') !== false || stripos($resp, 'error') !== false) {
                return ['ok' => false, 'message' => 'Robokassa: Неверная подпись (Проверьте Пароль2)'];
            }
            return ['ok' => true, 'message' => 'Ключи рабочие (API отвечает)'];
        }

        return ['ok' => false, 'message' => 'Robokassa: Ошибка API (HTTP ' . $code . ')'];
    }
}