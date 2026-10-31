<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Game-сервер (gs)
 */
class Gs extends ActiveRecord
{
    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    public static function tableName()
    {
        return '{{%gs}}';
    }

    public function getLs()
    {
        return $this->hasOne(Ls::class, ['id' => 'login_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
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
        
        $key = Yii::$app->request->cookieValidationKey;
        
        try {
            Yii::$app->security->decryptByKey($value, $key);
            $this->db_pass = $value;
        } catch (\Exception $e) {
            $this->db_pass = Yii::$app->security->encryptByKey($value, $key);
        }
    }

    public function getDbPassDecrypted()
    {
        if (empty($this->db_pass)) {
            return '';
        }
        
        $key = Yii::$app->request->cookieValidationKey;
        
        try {
            return Yii::$app->security->decryptByKey($this->db_pass, $key);
        } catch (\Exception $e) {
            return $this->db_pass;
        }
    }

    public static function findOpened()
    {
        return static::find()->where(['status' => self::STATUS_ON]);
    }

    public static function getOpenServers()
    {
        return Yii::$app->cache->getOrSet(
            'open_servers',
            fn() => static::findOpened()->orderBy(['id' => SORT_ASC])->all(),
            3600 * 24
        );
    }

    public function rules()
    {
        return [
            [['name', 'ip', 'port', 'db_host', 'db_port', 'db_user', 'db_name', 'login_id', 'version', 'status'], 'required'],
            [['port', 'login_id', 'status'], 'integer'],
            [['name', 'ip', 'db_host', 'db_user', 'db_pass', 'db_name', 'version'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => Yii::t('models', 'Название'),
            'ip'         => 'IP',
            'port'       => Yii::t('models', 'Порт'),
            'login_id'   => Yii::t('models', 'Login-сервер'),
            'status'     => Yii::t('models', 'Статус'),
            'created_at' => Yii::t('models', 'Создано'),
            'updated_at' => Yii::t('models', 'Обновлено'),
        ];
    }

    public static function isTopupMode(): bool
    {
        static $cache = null;
        if ($cache === null) {
            $cache = self::find()->where(['status' => 1, 'shop_mode' => 'topup'])->exists();
        }
        return $cache;
    }
}