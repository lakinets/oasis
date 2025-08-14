<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * @property int    $id
 * @property int    $user_id
 * @property int    $category_id
 * @property string $title
 * @property int    $status
 * @property int    $priority
 * @property int    $new_message_for_admin
 * @property int    $new_message_for_user
 * @property int    $gs_id
 * @property string $char_name
 * @property string $date_incident
 * @property int    $created_at
 * @property int    $updated_at
 *
 * @property Users               $user
 * @property TicketsCategories   $category
 * @property TicketsAnswers[]    $answers
 */
class Tickets extends ActiveRecord
{
    /* --- статусы --- */
    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 0;

    /* --- приоритеты --- */
    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    /* --- флаги новых сообщений --- */
    const STATUS_NEW_MESSAGE_OFF = 0;
    const STATUS_NEW_MESSAGE_ON  = 1;

    public static function tableName()
    {
        return 'tickets';
    }

    /* --- правила валидации --- */
    public function rules()
    {
        return [
            [['user_id', 'category_id', 'title', 'gs_id', 'priority'], 'required'],
            [['user_id', 'category_id', 'status', 'priority', 'new_message_for_admin',
              'new_message_for_user', 'gs_id'], 'integer'],
            [['char_name', 'title'], 'string', 'max' => 255],
            [['date_incident'], 'safe'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /* --- связи --- */
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

    /* --- списки для фильтров --- */
    public static function getStatusList()
    {
        return [
            self::STATUS_OPEN  => Yii::t('backend', 'Открыт'),
            self::STATUS_CLOSE => Yii::t('backend', 'Закрыт'),
        ];
    }

    public static function getPrioritiesList()
    {
        return [
            self::PRIORITY_LOW    => Yii::t('backend', 'Низкий'),
            self::PRIORITY_MEDIUM => Yii::t('backend', 'Средний'),
            self::PRIORITY_HIGH   => Yii::t('backend', 'Высокий'),
        ];
    }

    /* --- текстовые метки --- */
    public function getStatus()
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public function getPriority()
    {
        return self::getPrioritiesList()[$this->priority] ?? '-';
    }

    public function isStatusOn()
    {
        return $this->status == self::STATUS_OPEN;
    }

    public function isNewMessageForAdmin()
    {
        return $this->new_message_for_admin == self::STATUS_NEW_MESSAGE_ON
            ? Yii::t('backend', 'Да')
            : Yii::t('backend', 'Нет');
    }

    public function isNewMessageForUser()
    {
        return $this->new_message_for_user == self::STATUS_NEW_MESSAGE_ON
            ? Yii::t('backend', 'Да')
            : Yii::t('backend', 'Нет');
    }
}