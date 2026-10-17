<?php

namespace app\modules\cabinet\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;

class ShopCategories extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%shop_categories}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'link',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'link', 'sort', 'status', 'gs_id'], 'required'],
            [['sort', 'status', 'gs_id'], 'integer'],
            [['name', 'name_en', 'link'], 'string', 'max' => 255],
            [['link'], 'unique', 'targetAttribute' => ['link', 'gs_id']],
        ];
    }

    public function getPacks()
    {
        return $this->hasMany(ShopItemsPacks::class, ['category_id' => 'id']);
    }

    /**
     * Название категории с учётом языка. Fallback на RU.
     */
    public function getName($lang = null): string
    {
        $lang = $lang ?: Yii::$app->language;
        if ($lang === 'en' && $this->name_en !== null && $this->name_en !== '') {
            return (string)$this->name_en;
        }
        return (string)$this->name;
    }
}