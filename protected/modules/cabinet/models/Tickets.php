<?php
namespace app\modules\cabinet\models;

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
            'user_id'               => 'Пользователь',
            'category_id'           => 'Категория',
            'priority'              => 'Приоритет',
            'date_incident'         => 'Дата происшествия',
            'char_name'             => 'Имя персонажа',
            'title'                 => 'Заголовок',
            'status'                => 'Статус',
            'new_message_for_user'  => 'Новое сообщение пользователю',
            'new_message_for_admin' => 'Новое сообщение админу',
            'gs_id'                 => 'Сервер',
            'created_at'            => 'Создан',
            'updated_at'            => 'Обновлён',
        ];
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
        return [self::STATUS_CLOSED => 'Закрыт', self::STATUS_OPEN => 'Открыт'][$this->status] ?? '';
    }

    public function getPriorityLabel()
    {
        return self::getPriorityList()[$this->priority] ?? '';
    }

    public function getCategory()
    {
        // используем backend-модель, но через alias или полный namespace
        return $this->hasOne(\app\modules\backend\models\TicketsCategories::class, ['id' => 'category_id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(TicketsAnswers::class, ['ticket_id' => 'id']);
    }
}