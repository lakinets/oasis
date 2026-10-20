<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ShopTopupConfig extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_topup_config';
    }

    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['gs_id'], 'required'],
            [['gs_id', 'item_id', 'item_count', 'status'], 'integer'], // убран item_enchant
            [['price'], 'number'],
            [['currency'], 'string', 'max' => 10],
            [['item_name'], 'string', 'max' => 255],
            [['description_ru', 'description_en'], 'string'],
            [['item_count'], 'default', 'value' => 1],
            [['price'], 'default', 'value' => 0],
            [['currency'], 'default', 'value' => 'RUB'],
            [['status'], 'default', 'value' => 1],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'gs_id'          => Yii::t('backend', 'Сервер'),
            'item_id'        => Yii::t('backend', 'ID предмета'),
            'item_count'     => Yii::t('backend', 'Количество'),
            'item_name'      => Yii::t('backend', 'Название предмета'),
            'description_ru' => Yii::t('backend', 'Описание (RU)'),
            'description_en' => Yii::t('backend', 'Описание (EN)'),
            'price'          => Yii::t('backend', 'Цена'),
            'currency'       => Yii::t('backend', 'Валюта'),
            'status'         => Yii::t('backend', 'Активен'),
        ];
    }

    // Связь с порогами (вместо старых бонусов)
    public function getTiers()
    {
        return $this->hasMany(ShopTopupTier::class, ['topup_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC]);
    }

    public static function getCurrencyList(): array
    {
        return [
            'RUB'  => 'RUB',
            'USD'  => 'USD',
            'EUR'  => 'EUR',
            'USDT' => 'USDT',
        ];
    }

    public static function findForServer(int $gsId): ?self
    {
        return self::find()->where(['gs_id' => $gsId])->one();
    }
}