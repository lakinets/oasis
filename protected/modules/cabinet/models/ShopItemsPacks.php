<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;
use app\modules\cabinet\models\ShopCategories;   // ← новый use

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

    /**
     * Связь с категорией
     */
    public function getCategory()
    {
        return $this->hasOne(ShopCategories::class, ['id' => 'category_id']);
    }
}