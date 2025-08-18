<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class TicketsAnswers extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%tickets_answers}}';
    }

    public function rules()
    {
        return [
            [['ticket_id', 'text', 'user_id'], 'required'],
            [['ticket_id', 'user_id'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'user_id']);
    }
}