<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class ShopItems extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%shop_items}}';
    }

    public function rules()
    {
        return [
            [['pack_id', 'item_id', 'count', 'enchant', 'status'], 'required'],
            [['pack_id', 'item_id', 'count', 'enchant', 'status'], 'integer'],
            [['cost', 'discount'], 'number'],
        ];
    }

    public function getItemInfo()
    {
        return $this->hasOne(\app\models\AllItems::class, ['item_id' => 'item_id']);
    }
}