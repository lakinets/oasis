<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class ShopItems extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_items';
    }

    public function rules()
    {
        return [
            [['pack_id', 'item_id', 'cost', 'count'], 'required'],
            [['pack_id', 'item_id', 'count', 'enchant', 'sort', 'status'], 'integer'],
            [['cost', 'discount'], 'number'],
            [['currency_type'], 'string', 'max' => 54],
            [['description'], 'string'],
        ];
    }

    /**
     * Связь с паком
     */
    public function getPack()
    {
        return $this->hasOne(ShopItemsPacks::class, ['id' => 'pack_id']);
    }

    /**
     * Связь с таблицей всех предметов
     */
    public function getItemInfo()
    {
        return $this->hasOne(AllItems::class, ['item_id' => 'item_id']);
    }
}