<?php
namespace app\modules\cabinet\models;

use yii\base\Model;
use app\components\payments\AppConfig;

class DepositForm extends Model
{
    public $amount;
    public $payment_system;

    public function rules()
    {
        // Читаем лимиты из базы (или ставим дефолтные)
        $min = (float)AppConfig::get('payment.min_amount', 10);
        $max = (float)AppConfig::get('payment.max_amount', 100000);

        return [
            [['amount', 'payment_system'], 'required'],
            [['amount'], 'number', 'min' => $min, 'max' => $max],
            [['payment_system'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => Yii::t('views', 'Сумма (Web Adena)'),
            'payment_system' => Yii::t('views', 'Платёжная система'),
        ];
    }
}