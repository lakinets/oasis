<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class TicketsAnswers extends ActiveRecord
{
    public static function tableName()
    {
        return 'tickets_answers';
    }

    /**
     * Связь с моделью пользователя.
     * Используем правильное имя класса и ключевые поля,
     * так как в таблице users PK — user_id.
     */
    public function getUserInfo()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }
}