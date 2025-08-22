<?php
namespace app\components;

use Yii;
use yii\db\Exception;

class AppConfig
{
    /** Простая точечная адресация: 'register.allow', 'prefixes.allow' и т.п. */
    public static function get(string $path, $default = null)
    {
        // 1) params
        $val = self::arrayGet(Yii::$app->params, $path);
        // 2) config table (перекрывает params при наличии)
        try {
            $row = (new \yii\db\Query())
                ->from('config')
                ->select(['value'])
                ->where(['param' => $path])
                ->limit(1)
                ->one(Yii::$app->db);
            if ($row && array_key_exists('value', $row)) {
                $fromDb = $row['value'];
                // Пробуем json -> массив/скаляр
                $json = json_decode($fromDb, true);
                return $json === null && json_last_error() !== JSON_ERROR_NONE ? $fromDb : $json;
            }
        } catch (\Throwable $e) {
            // тихо игнорируем, чтобы не валить фронт, если таблицы ещё нет
        }
        return $val !== null ? $val : $default;
    }

    private static function arrayGet(array $arr, string $path)
    {
        $cur = $arr;
        foreach (explode('.', $path) as $seg) {
            if (!is_array($cur) || !array_key_exists($seg, $cur)) return null;
            $cur = $cur[$seg];
        }
        return $cur;
    }

    /** Удобные геттеры с дефолтами */
    public static function registerEnabled(): bool
    {
        return (int) self::get('register.allow', 1) === 1;
    }
    public static function captchaEnabled(): bool
    {
        return (int) self::get('register.captcha.allow', 0) === 1;
    }
    public static function emailConfirmEnabled(): bool
    {
        return (int) self::get('register.confirm_email.allow', 0) === 0 ? false : (int) self::get('register.confirm_email.allow', 0) === 1;
    }
    public static function emailConfirmTTLMinutes(): int
    {
        return (int) self::get('register.confirm_email.time', 60);
    }
    public static function prefixesEnabled(): bool
    {
        return (int) self::get('prefixes.allow', 0) === 1;
    }
    public static function prefixLength(): int
    {
        return (int) self::get('prefixes.length', 3);
    }
    /** Массив допустимых префиксов; если пуст — генерим случайные */
    public static function prefixList(): array
    {
        $raw = self::get('prefixes.list', []);
        if (is_string($raw)) $raw = array_filter(array_map('trim', explode(',', $raw)));
        return is_array($raw) ? $raw : [];
    }
    public static function referralsEnabled(): bool
    {
        return (int) self::get('referral_program.allow', 0) === 1;
    }
}
