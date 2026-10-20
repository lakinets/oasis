<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

class ShopTopupTier extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_topup_tiers';
    }

    public function rules()
    {
        return [
            [['topup_id', 'min_qty', 'max_qty', 'bonus_qty', 'sort'], 'integer'],
            [['min_qty', 'bonus_qty'], 'required'],
            [['max_qty'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'min_qty'   => Yii::t('backend', 'От (кол-во)'),
            'max_qty'   => Yii::t('backend', 'До (кол-во)'),
            'bonus_qty' => Yii::t('backend', 'Бонус (кол-во)'),
        ];
    }

    public function getTopup()
    {
        return $this->hasOne(ShopTopupConfig::class, ['id' => 'topup_id']);
    }
}