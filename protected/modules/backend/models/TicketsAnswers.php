<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Class TicketsAnswers
 * @package app\modules\backend\models
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $text
 * @property string $created_at
 *
 * @property Tickets $ticket
 * @property Users $userInfo
 */
class TicketsAnswers extends ActiveRecord
{
    public static function tableName()
    {
        return 'tickets_answers';
    }

    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'text'], 'required'],
            [['ticket_id', 'user_id'], 'integer'],
            [['text'], 'string'],
            // created_at заполняем вручную
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('backend', 'ID'),
            'ticket_id' => Yii::t('backend', 'Тикет'),
            'user_id'   => Yii::t('backend', 'Пользователь'),
            'text'      => Yii::t('backend', 'Сообщение'),
            'created_at'=> Yii::t('backend', 'Дата создания'),
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(Tickets::class, ['id' => 'ticket_id']);
    }

    public function getUserInfo()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }
}