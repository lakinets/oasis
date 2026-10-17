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
        $dir = Yii::getAlias('@app/runtime/logs');
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true); // Автосоздание папки логов
        }
        $log = $dir . '/app.log';
        return is_writable($dir) && (!is_file($log) || is_writable($log));
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
        if (stripos(PHP_OS, 'WIN') === 0) {
            return null;
        }
        $dir = Yii::getAlias('@app/modules/install');
        return is_dir($dir) && is_writable($dir);
    }

    // Считаем количество невыполненных требований
    public function getFailedCount(): int
    {
        $failed = 0;
        $failed += $this->checkPhp() ? 0 : 1;
        $failed += $this->checkPdo() ? 0 : 1;
        $failed += $this->checkGd() ? 0 : 1;
        $failed += $this->checkModRewrite() ? 0 : 1;
        $failed += $this->checkLogWritable() ? 0 : 1;
        $failed += $this->checkOnlineTxtWritable() ? 0 : 1;
        $failed += $this->checkRobotsTxtWritable() ? 0 : 1;
        if ($this->checkInstallDirWritable() !== null) {
            $failed += $this->checkInstallDirWritable() ? 0 : 1;
        }
        return $failed;
    }
}