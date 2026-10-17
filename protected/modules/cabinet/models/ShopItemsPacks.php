<?php

namespace app\modules\cabinet\models;

use Yii;
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
            [['title', 'title_en'], 'string', 'max' => 255],
            [['description', 'description_en'], 'string'],
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

    /**
     * Название пака с учётом языка. Fallback на RU.
     */
    public function getTitle($lang = null): string
    {
        $lang = $lang ?: Yii::$app->language;
        if ($lang === 'en' && $this->title_en !== null && $this->title_en !== '') {
            return (string)$this->title_en;
        }
        return (string)$this->title;
    }

    /**
     * Описание пака с учётом языка. Fallback на RU.
     */
    public function getDescription($lang = null): string
    {
        $lang = $lang ?: Yii::$app->language;
        if ($lang === 'en' && $this->description_en !== null && $this->description_en !== '') {
            return (string)$this->description_en;
        }
        return (string)($this->description ?? '');
    }
}