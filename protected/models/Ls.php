<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%ls}}".
 *
 * @property int $id
 * @property string $name
 * @property string $ip
 * @property string $port
 * @property string $db_host
 * @property string $db_port
 * @property string $db_user
 * @property string $db_pass
 * @property string $db_name
 * @property string $version
 * @property int $status
 * @property string $password_type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Gs[] $servers
 */
class Ls extends ActiveRecord
{
    const PASSWORD_TYPE_SHA1     = 'sha1';
    const PASSWORD_TYPE_WIRLPOOL = 'wirlpool';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ls}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ip', 'port', 'db_host', 'db_port', 'db_user', 'db_name', 'version', 'status', 'password_type'], 'filter', 'filter' => 'trim'],
            [['name', 'ip', 'port', 'db_host', 'db_port', 'db_user', 'db_name', 'version', 'status', 'password_type'], 'required'],
            [['status'], 'in', 'range' => [self::STATUS_ON, self::STATUS_OFF]],
            [['name', 'ip', 'db_host', 'db_user', 'db_pass', 'db_name'], 'string', 'max' => 54],
            [['port'], 'string', 'max' => 5],
            [['version'], 'in', 'range' => array_keys(\Yii::$app->params['server_versions'] ?? [])],
            [['password_type'], 'in', 'range' => array_keys($this->getPasswordTypeList())],
            [['db_pass'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Связь с серверами
     */
    public function getServers()
    {
        return $this->hasMany(Gs::class, ['login_id' => 'id']);
    }

    /**
     * Список типов пароля
     */
    public function getPasswordTypeList()
    {
        return [
            self::PASSWORD_TYPE_SHA1     => 'sha1',
            self::PASSWORD_TYPE_WIRLPOOL => 'wirlpool',
        ];
    }

    /**
     * Получить текстовое название типа пароля
     */
    public function getPasswordType()
    {
        $data = $this->getPasswordTypeList();
        return $data[$this->password_type] ?? \Yii::t('backend', '*Unknown*');
    }

    /**
     * Список статусов
     */
    public function getStatusList()
    {
        return [
            self::STATUS_ON  => \Yii::t('backend', 'Включен'),
            self::STATUS_OFF => \Yii::t('backend', 'Отключен'),
        ];
    }

    /**
     * Scope: только активные
     */
    public static function findOpened()
    {
        return self::find()->where(['status' => self::STATUS_ON]);
    }

    /**
     * Список открытых логинов (с кэшем)
     */
    public static function getOpenLoginServers()
    {
        return \Yii::$app->cache->getOrSet('open_login_servers', function () {
            return self::findOpened()->all();
        }, 3600 * 24);
    }

    /**
     * Поиск (для GridView)
     */
    public function search()
    {
        $query = self::find()->where(['status' => [self::STATUS_ON, self::STATUS_OFF]]);

        $query->orderBy(['created_at' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
                'pageVar'  => 'page',
            ],
        ]);
    }

    /* Геттеры для удобства */
    public function getName()   { return $this->name; }
    public function getIp()     { return $this->ip; }
    public function getPort()   { return $this->port; }
    public function getDbHost() { return $this->db_host; }
    public function getDbPort() { return $this->db_port; }
    public function getDbUser() { return $this->db_user; }
    public function getDbPass() { return $this->db_pass; }
    public function getDbName() { return $this->db_name; }
    public function getVersion() { return $this->version; }

    const STATUS_ON  = 1;
    const STATUS_OFF = 0;
}