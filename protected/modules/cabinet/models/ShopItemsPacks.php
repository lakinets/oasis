<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class ShopItemsPacks extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%shop_items_packs}}';
    }

    public function rules()
    {
        return [
            [['title', 'category_id', 'sort', 'status'], 'required'],
            [['category_id', 'sort', 'status'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function getItems()
    {
        return $this->hasMany(ShopItems::class, ['pack_id' => 'id']);
    }
}