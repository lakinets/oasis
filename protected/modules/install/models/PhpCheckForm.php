<?php
namespace app\modules\install\models;

use Yii;
use yii\base\Model;

class PhpCheckForm extends Model
{
    public function checkPhp(): bool  { return PHP_VERSION_ID >= 80200; }
    public function checkPdo(): bool  { return extension_loaded('pdo_mysql'); }
    public function checkGd(): bool   { return extension_loaded('gd'); }

    public function checkModRewrite(): bool
    {
        return function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : false;
    }

    public function checkLogWritable(): bool
    {
        $log = Yii::getAlias('@app/runtime/logs/app.log');
        return is_writable(dirname($log)) && (!is_file($log) || is_writable($log));
    }

    public function checkOnlineTxtWritable(): bool
    {
        $file = Yii::getAlias('@webroot/online.txt');
        return is_writable(dirname($file)) && (!is_file($file) || is_writable($file));
    }

    public function checkRobotsTxtWritable(): bool
    {
        $file = Yii::getAlias('@webroot/robots.txt');
        return is_writable(dirname($file)) && (!is_file($file) || is_writable($file));
    }

    public function checkInstallDirWritable(): ?bool
    {
        // На Windows не проверяем — всегда true
        if (stripos(PHP_OS, 'WIN') === 0) {
            return null; // спец. значение для "пропустить проверку"
        }

        $dir = Yii::getAlias('@app/protected/modules/install');
        return is_dir($dir) && is_writable($dir);
    }
}
