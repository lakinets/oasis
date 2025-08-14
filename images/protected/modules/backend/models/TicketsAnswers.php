<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class TicketsAnswers extends ActiveRecord
{
    public static function tableName() { return 'tickets_answers'; }

    public function getUserInfo()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}