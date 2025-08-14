<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class ShopCategories extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_categories';
    }

    public function rules()
    {
        return [
            [['name', 'link', 'category_id'], 'required'],
            [['sort', 'status', 'gs_id'], 'integer'],
            [['name', 'link'], 'string', 'max' => 255],
        ];
    }

    public function isStatusOn()
    {
        return $this->status == 1;
    }

    public function getStatusLabel()
    {
        return $this->isStatusOn() ? Yii::t('backend', 'Включён') : Yii::t('backend', 'Выключен');
    }

    public function getPacks()
    {
        return $this->hasMany(ShopItemsPacks::class, ['category_id' => 'id']);
    }
}