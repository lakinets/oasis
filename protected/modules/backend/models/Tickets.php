<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use Yii;

class Tickets extends ActiveRecord
{
    const STATUS_CLOSED = 0;
    const STATUS_OPEN   = 1;

    const PRIORITY_LOW    = 0;
    const PRIORITY_MEDIUM = 1;
    const PRIORITY_HIGH   = 2;

    const STATUS_NEW_MESSAGE_OFF = 0;
    const STATUS_NEW_MESSAGE_ON  = 1;

    public static function tableName()
    {
        return 'tickets';
    }

    public function behaviors()
    {
        // пусто — created_at / updated_at заполняем вручную
        return [];
    }

    public function rules()
    {
        return [
            [['user_id', 'category_id', 'title', 'priority', 'gs_id'], 'required'],
            [['user_id', 'category_id', 'status', 'priority', 'gs_id', 'new_message_for_user', 'new_message_for_admin'], 'integer'],
            [['title', 'char_name'], 'string', 'max' => 255],
            [['date_incident'], 'safe'],
        ];
    }

    public static function getStatusList()
    {
        return [self::STATUS_CLOSED => 'Закрыт', self::STATUS_OPEN => 'Открыт'];
    }

    public static function getPriorityList()
    {
        return [
            self::PRIORITY_LOW    => 'Низкий',
            self::PRIORITY_MEDIUM => 'Средний',
            self::PRIORITY_HIGH   => 'Высокий',
        ];
    }

    public function getStatusLabel()
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public function getPriorityLabel()
    {
        return self::getPriorityList()[$this->priority] ?? '-';
    }

    public function isStatusOn()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isNewMessageForAdmin()
    {
        return $this->new_message_for_admin === self::STATUS_NEW_MESSAGE_ON;
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(TicketsCategories::class, ['id' => 'category_id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(TicketsAnswers::class, ['ticket_id' => 'id']);
    }
}