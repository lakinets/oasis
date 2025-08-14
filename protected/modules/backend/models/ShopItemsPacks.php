<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class ShopItemsPacks extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_items_packs';
    }

    public function rules()
    {
        return [
            [['title', 'category_id'], 'required'],
            [['category_id', 'sort', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    public function getItems()
    {
        return $this->hasMany(ShopItems::class, ['pack_id' => 'id']);
    }

    public function getCategory()
    {
        return $this->hasOne(ShopCategories::class, ['id' => 'category_id']);
    }
}