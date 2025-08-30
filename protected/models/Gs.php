<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Game-сервер (gs)
 *
 * @property int    $id
 * @property string $name
 * @property string $ip
 * @property int    $port
 * @property string $db_host
 * @property int    $db_port
 * @property string $db_user
 * @property string $db_pass
 * @property string $db_name
 * @property int    $login_id
 * @property string $version
 * @property int    $status
 * @property string $created_at
 * @property string $updated_at
 */
class Gs extends ActiveRecord
{
    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    public static function tableName()
    {
        return '{{%gs}}';
    }

    /* ---------------- связи ---------------- */
    public function getLs()
    {
        return $this->hasOne(Ls::class, ['id' => 'login_id']);
    }

    /* ---------------- поведения ---------------- */
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

    /* ---------------- запросы ---------------- */
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

    /* ---------------- правила ---------------- */
    public function rules()
    {
        return [
            [['name', 'ip', 'port', 'db_host', 'db_port', 'db_user', 'db_name', 'login_id', 'version', 'status'], 'required'],
            [['port', 'login_id', 'status'], 'integer'],
            [['name', 'ip', 'db_host', 'db_user', 'db_pass', 'db_name', 'version'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /* ---------------- подписи ---------------- */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Название',
            'ip'         => 'IP',
            'port'       => 'Порт',
            'login_id'   => 'Login-сервер',
            'status'     => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }
}