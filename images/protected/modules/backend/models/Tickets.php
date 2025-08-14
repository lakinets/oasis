<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class Tickets extends ActiveRecord
{
    public static function tableName() { return 'tickets'; }

    public function getCategory()
    {
        return $this->hasOne(TicketsCategories::class, ['id' => 'category_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}