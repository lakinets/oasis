<?php

namespace app\modules\cabinet\models;

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
            [['name', 'link'], 'string', 'max' => 255],
            [['link'], 'unique', 'targetAttribute' => ['link', 'gs_id']],
        ];
    }

    public function getPacks()
    {
        return $this->hasMany(ShopItemsPacks::class, ['category_id' => 'id']);
    }
}