<?php
namespace app\models;

use yii\db\ActiveRecord;

class Config extends ActiveRecord
{
    public static function tableName()
    {
        return 'config';
    }

    /**
     * Возвращает значение параметра или значение по умолчанию
     * @param string $param
     * @return string
     */
    public static function value($param)
    {
        $row = static::find()->where(['param' => $param])->one();
        return $row ? $row->value : '';
    }

    /**
     * Тема сайта
     */
    public static function theme()
    {
        return static::value('theme') ?: 'default';
    }
}