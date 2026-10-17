<?php
namespace app\modules\cabinet\models;

use Yii;
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
            'category_id'   => Yii::t('models', 'Категория'),
            'gs_id'         => Yii::t('models', 'Сервер'),
            'priority'      => Yii::t('models', 'Приоритет'),
            'title'         => Yii::t('models', 'Заголовок'),
            'char_name'     => Yii::t('models', 'Имя персонажа'),
            'date_incident' => Yii::t('models', 'Дата происшествия'),
            'text'          => Yii::t('models', 'Сообщение'),
        ];
    }
}