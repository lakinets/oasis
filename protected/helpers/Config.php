<?php
namespace app\helpers;

use Yii;

class Config
{
    private static $_cache = [];

    /**
     * Возвращает значение параметра из таблицы `config`.
     * @param string $param
     * @param mixed  $default
     * @return mixed
     */
    public static function get($param, $default = null)
    {
        if (!array_key_exists($param, self::$_cache)) {
            $row = Yii::$app->db->createCommand(
                'SELECT `value` FROM config WHERE param = :p',
                [':p' => $param]
            )->queryOne();
            self::$_cache[$param] = $row ? $row['value'] : $default;
        }
        return self::$_cache[$param];
    }

    /**
     * Быстрая проверка "включено ли что-то".
     * @param string $param
     * @return bool
     */
    public static function isOn($param)
    {
        return (bool)(int)static::get($param, 0);
    }
}