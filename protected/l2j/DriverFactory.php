<?php
namespace app\l2j;

use yii\db\Connection;

class DriverFactory
{
    /**
     * Возвращает экземпляр драйвера по строке версии
     * @throws \RuntimeException если класс не найден
     */
    public static function make(Connection $db, string $version, ?string $dbName = null): object
    {
        $class = 'app\\l2j\\' . $version;
        if (!class_exists($class)) {
            throw new \RuntimeException("Драйвер $class не найден");
        }
        return new $class($db, $dbName);
    }
}