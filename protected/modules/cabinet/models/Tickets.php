<?php
namespace app\modules\cabinet\models;

use Yii;
use yii\db\ActiveRecord;

class Tickets extends ActiveRecord
{
    const STATUS_CLOSED = 0;
    const STATUS_OPEN   = 1;

    const PRIORITY_LOW    = 0;
    const PRIORITY_MEDIUM = 1;
    const PRIORITY_HIGH   = 2;

    public static function tableName()
    {
        return '{{%tickets}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'category_id', 'priority', 'title', 'char_name', 'date_incident', 'gs_id'], 'required'],
            [['user_id', 'category_id', 'priority', 'status', 'new_message_for_user', 'new_message_for_admin', 'gs_id'], 'integer'],
            [['title', 'char_name', 'date_incident'], 'string', 'max' => 255],
            [['text'], 'safe'], // текст хранится в answers
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'user_id'               => Yii::t('models', 'Пользователь'),
            'category_id'           => Yii::t('models', 'Категория'),
            'priority'              => Yii::t('models', 'Приоритет'),
            'date_incident'         => Yii::t('models', 'Дата происшествия'),
            'char_name'             => Yii::t('models', 'Имя персонажа'),
            'title'                 => Yii::t('models', 'Заголовок'),
            'status'                => Yii::t('models', 'Статус'),
            'new_message_for_user'  => Yii::t('models', 'Новое сообщение пользователю'),
            'new_message_for_admin' => Yii::t('models', 'Новое сообщение админу'),
            'gs_id'                 => Yii::t('models', 'Сервер'),
            'created_at'            => Yii::t('models', 'Создан'),
            'updated_at'            => Yii::t('models', 'Обновлён'),
        ];
    }

    public static function getPriorityList()
    {
        return [
            self::PRIORITY_LOW    => Yii::t('models', 'Низкий'),
            self::PRIORITY_MEDIUM => Yii::t('models', 'Средний'),
            self::PRIORITY_HIGH   => Yii::t('models', 'Высокий'),
        ];
    }

    public function getStatusLabel()
    {
        return [self::STATUS_CLOSED => Yii::t('models', 'Закрыт'), self::STATUS_OPEN => Yii::t('models', 'Открыт')][$this->status] ?? '';
    }

    public function getPriorityLabel()
    {
        return self::getPriorityList()[$this->priority] ?? '';
    }

    public function getCategory()
    {
        return $this->hasOne(\app\modules\backend\models\TicketsCategories::class, ['id' => 'category_id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(TicketsAnswers::class, ['ticket_id' => 'id']);
    }
}