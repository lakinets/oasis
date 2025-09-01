<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class ShopCategories extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_categories';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => fn() => date('Y-m-d H:i:s'), // или `time()` если поле INT
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'link', 'sort', 'status', 'gs_id'], 'required'],
            [['sort', 'status', 'gs_id'], 'integer'],
            [['name', 'link'], 'string', 'max' => 255],
            [['created_at'], 'safe'], // если поле DATETIME
        ];
    }

    public function getPacks()
    {
        return $this->hasMany(ShopItemsPacks::class, ['category_id' => 'id']);
    }
}