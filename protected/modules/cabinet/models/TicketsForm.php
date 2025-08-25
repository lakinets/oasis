<?php
namespace app\modules\cabinet\models;

use yii\base\Model;

class TicketsForm extends Model
{
    public $category_id;
    public $gs_id      = 1;
    public $priority   = 1;
    public $title;
    public $char_name;
    public $date_incident;
    public $text;

    public function rules()
    {
        return [
            [['category_id', 'gs_id', 'priority', 'title', 'char_name', 'date_incident', 'text'], 'required'],
            [['category_id', 'gs_id', 'priority'], 'integer'],
            [['title', 'char_name', 'date_incident'], 'string', 'max' => 255],
            [['text'], 'string', 'min' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            'category_id'   => 'Категория',
            'gs_id'         => 'Сервер',
            'priority'      => 'Приоритет',
            'title'         => 'Заголовок',
            'char_name'     => 'Имя персонажа',
            'date_incident' => 'Дата происшествия',
            'text'          => 'Сообщение',
        ];
    }
}