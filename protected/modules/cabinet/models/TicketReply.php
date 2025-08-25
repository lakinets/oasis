<?php
namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

/**
 * TicketReply
 * Та же таблица, что и TicketsAnswers, но с другим именем класса (для формы).
 */
class TicketReply extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%ticket_answers}}'; // та же таблица
    }

    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'text'], 'required'],
            [['ticket_id', 'user_id'], 'integer'],
            [['text'], 'string', 'max' => 5000],
            [['created_at'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && empty($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }
}