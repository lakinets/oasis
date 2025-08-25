<?php

namespace app\components;

use Yii;
use yii\db\Connection;
use app\models\Gs;

class Lineage
{
    private static $_instance = [];

    protected $_config;
    protected $_db;
    protected $_query;

    public static function getInstance($type, $id)
    {
        $type = strtolower($type);
        $id   = (int)$id;

        if (!isset(self::$_instance[$type][$id])) {
            self::$_instance[$type][$id] = new self($type, $id);
        }

        return self::$_instance[$type][$id];
    }

    public static function gs($id)
    {
        return self::getInstance('gs', $id);
    }

    private function __construct($type, $id)
    {
        $this->init($type, $id);
    }

    private function init($type, $id)
    {
        if ($type === 'gs') {
            $this->_config = Gs::find()
                ->where(['status' => 1, 'id' => $id])
                ->one();
        }

        if (!$this->_config) {
            throw new \Exception("Настройки в БД для {$type} с ID {$id} не найдены");
        }
    }

    public function connect()
    {
        if (!$this->_db) {
            $this->_db = new Connection([
                'dsn'      => "mysql:host={$this->config('db_host')};port={$this->config('db_port')};dbname={$this->config('db_name')}",
                'username' => $this->config('db_user'),
                'password' => $this->config('db_pass'),
                'charset'  => 'utf8',
            ]);
        }

        $this->loadQuery();
        return $this;
    }

    private function loadQuery()
    {
        $version = ucfirst($this->config('version'));
        $class   = 'app\\l2j\\' . $version;

        if (!class_exists($class)) {
            throw new \Exception("Класс {$class} не найден");
        }

        $this->_query = new $class($this->_db);
    }

    public function config($key, $default = null)
    {
        return $this->_config->$key ?? $default;
    }

    public function __call($method, $params)
    {
        if (method_exists($this->_query, $method)) {
            return call_user_func_array([$this->_query, $method], $params);
        }
        throw new \BadMethodCallException("Метод {$method} не найден");
    }

    /* ---------- Хелперы (опционально) ---------- */
    public static function getOnlineTime($time)
    {
        $hours   = floor($time / 3600);
        $minutes = floor(($time % 3600) / 60);
        return "{$hours}ч {$minutes}м";
    }

    /**
     * Возвращает объект подключения к БД сервера.
     * @return Connection
     */
    public function getDb(): Connection
    {
        return $this->_db;
    }
}