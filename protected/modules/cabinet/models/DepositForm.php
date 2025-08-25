<?php
namespace app\modules\cabinet\models;

use yii\base\Model;

class DepositForm extends Model
{
    public $amount;
    public $payment_system;

    public function rules()
    {
        return [
            [['amount', 'payment_system'], 'required'],
            [['amount'], 'integer', 'min' => 1],
            [['payment_system'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => 'Сумма (Web Adena)',
            'payment_system' => 'Платёжная система',
        ];
    }
}
