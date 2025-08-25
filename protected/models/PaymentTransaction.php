<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $order_id
 * @property string $provider
 * @property string|null $provider_invoice
 * @property int $user_id
 * @property string $login
 * @property float $amount
 * @property string $currency
 * @property string $status
 * @property string|null $payload
 * @property string $created_at
 * @property string|null $paid_at
 */
class PaymentTransaction extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'payment_transactions';
    }

    public static function create(string $provider, int $userId, string $login, float $amount, string $currency = 'RUB'): self
    {
        $m = new self();
        $m->order_id  = self::genOrderId();
        $m->provider  = $provider;
        $m->user_id   = $userId;
        $m->login     = $login;
        $m->amount    = $amount;
        $m->currency  = $currency;
        $m->status    = 'new';
        $m->created_at= date('Y-m-d H:i:s');
        if (!$m->save(false)) {
            throw new \RuntimeException('Cannot save payment transaction');
        }
        return $m;
    }

    public static function genOrderId(): string
    {
        return strtoupper(dechex(time())) . bin2hex(random_bytes(4));
    }
}
