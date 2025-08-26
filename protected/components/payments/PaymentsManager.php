<?php
namespace app\components\payments;

use Yii;

final class PaymentsManager
{
    public static function available(): array
    {
        $list = [];

        if (self::isOn('payment.robokassa.enabled'))   $list['robokassa']   = 'Robokassa';
        if (self::isOn('payment.unitpay.enabled'))     $list['unitpay']     = 'Unitpay';
        if (self::isOn('payment.nowpayments.enabled')) $list['nowpayments'] = 'NOWPayments (crypto)';
        if (self::isOn('payment.payop.enabled'))       $list['payop']       = 'PayOp';
        if (self::isOn('payment.cryptomus.enabled'))   $list['cryptomus']   = 'Cryptomus (crypto)';
        if (self::isOn('payment.volet.enabled'))       $list['volet']       = 'Volet.com';
        if (self::isOn('payment.interkassa.enabled'))  $list['interkassa']  = 'Interkassa';

        return $list;
    }

    private static function isOn(string $param): bool
    {
        return ((int)\app\components\payments\AppConfig::get($param, 0)) === 1;
    }
}