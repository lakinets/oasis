<?php
namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class TicketsAnswers extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%tickets_answers}}'; // общая таблица
    }

    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'text'], 'required'],
            [['ticket_id', 'user_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(Tickets::class, ['id' => 'ticket_id']);
    }
}