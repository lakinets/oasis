<?php
namespace app\components\payments;

use Yii;

class AppConfig
{
    private static array $cache = [];

    public static function get(string $param, $default = null)
    {
        if (isset(self::$cache[$param])) {
            return self::$cache[$param];
        }
        $val = (new \yii\db\Query())
            ->from('config')
            ->select('value')
            ->where(['param' => $param])
            ->scalar();
        if ($val === false || $val === null) {
            return $default;
        }
        return self::$cache[$param] = $val;
    }

    public static function getBool(string $param, bool $default = false): bool
    {
        $v = self::get($param, $default ? '1' : '0');
        return in_array((string)$v, ['1','true','yes','on'], true);
    }
}
