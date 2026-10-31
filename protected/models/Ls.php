<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Модель Login-сервера (таблица `ls`)
 */
class Ls extends ActiveRecord
{
    const PASSWORD_TYPE_SHA1     = 'sha1';
    const PASSWORD_TYPE_WIRLPOOL = 'wirlpool';

    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    public static function tableName()
    {
        return '{{%ls}}';
    }

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

    // =========================================================
    // === Шифрование пароля БД ===
    // =========================================================
    public function setDbPass($value)
    {
        if (empty($value)) {
            return;
        }
        
        $key = \Yii::$app->request->cookieValidationKey;
        
        // Проверяем, не зашифрован ли пароль уже
        try {
            \Yii::$app->security->decryptByKey($value, $key);
            $this->db_pass = $value; // Уже зашифрован
        } catch (\Exception $e) {
            // Если не удалось расшифровать, значит он в открытом виде — шифруем
            $this->db_pass = \Yii::$app->security->encryptByKey($value, $key);
        }
    }

    public function getDbPassDecrypted()
    {
        if (empty($this->db_pass)) {
            return '';
        }
        
        $key = \Yii::$app->request->cookieValidationKey;
        
        try {
            return \Yii::$app->security->decryptByKey($this->db_pass, $key);
        } catch (\Exception $e) {
            // Если не удалось расшифровать, возвращаем как есть (для старых записей)
            return $this->db_pass; 
        }
    }

    public function getServers()
    {
        return $this->hasMany(Gs::class, ['login_id' => 'id']);
    }

    public function getPasswordTypeList()
    {
        return [
            self::PASSWORD_TYPE_SHA1     => 'sha1',
            self::PASSWORD_TYPE_WIRLPOOL => 'wirlpool',
        ];
    }

    public function getPasswordType()
    {
        $data = $this->getPasswordTypeList();
        return $data[$this->password_type] ?? \Yii::t('backend', '*Unknown*');
    }

    public function getStatusList()
    {
        return [
            self::STATUS_ON  => \Yii::t('backend', 'Включен'),
            self::STATUS_OFF => \Yii::t('backend', 'Отключен'),
        ];
    }

    public static function findOpened()
    {
        return self::find()->where(['status' => self::STATUS_ON]);
    }

    public static function getOpenLoginServers()
    {
        return \Yii::$app->cache->getOrSet('open_login_servers', function () {
            return self::findOpened()->all();
        }, 3600 * 24);
    }

    public function search()
    {
        $query = self::find()->where(['status' => [self::STATUS_ON, self::STATUS_OFF]]);
        $query->orderBy(['created_at' => SORT_DESC]);

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
                'pageVar'  => 'page',
            ],
        ]);
    }

    public function getName()   { return $this->name; }
    public function getIp()     { return $this->ip; }
    public function getPort()   { return $this->port; }
    public function getDbHost() { return $this->db_host; }
    public function getDbPort() { return $this->db_port; }
    public function getDbUser() { return $this->db_user; }
    public function getDbPass() { return $this->db_pass; } // Осторожно: возвращает зашифрованную строку!
    public function getDbName() { return $this->db_name; }
    public function getVersion() { return $this->version; }
}