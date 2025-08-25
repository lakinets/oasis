<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class TicketsCategories extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%tickets_categories}}';
    }

    public static function list()
    {
        return static::find()->select(['name', 'id'])->indexBy('id')->column();
    }

    public function getTickets()
    {
        return $this->hasMany(Tickets::class, ['category_id' => 'id']);
    }
}