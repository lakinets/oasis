<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property string $page   алиас страницы (уникален)
 * @property string $title  заголовок
 * @property string $text   html-содержимое
 * @property int    $status 1 – опубликована, 0 – скрыта
 */
class Pages extends ActiveRecord
{
    public static function tableName()
    {
        return 'pages';   // имя таблицы в БД
    }
}