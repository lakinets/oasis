<?php

namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;
use app\modules\backend\models\LoginServers;

/**
 * Модель LoginServers (таблица `ls`)
 *
 * @property int         $id
 * @property string      $name
 * @property string      $ip
 * @property int         $port
 * @property string      $db_host
 * @property int         $db_port
 * @property string      $db_user
 * @property string|null $db_pass
 * @property string      $db_name
 * @property string|null $telnet_host
 * @property int|null    $telnet_port
 * @property string|null $telnet_pass
 * @property string      $version
 * @property string      $password_type
 * @property int         $status
 * @property string      $created_at
 * @property string|null $updated_at
 */
class LoginServers extends ActiveRecord
{
    public const STATUS_ON      = 1;
    public const STATUS_OFF     = 0;
    public const STATUS_DELETED = 9; // используется в LoginServersSearch

    public static function tableName(): string
    {
        return '{{%ls}}';
    }

    public function rules(): array
    {
        return [
            // обязательные поля
            [
                [
                    'name',
                    'ip',
                    'port',
                    'db_host',
                    'db_port',
                    'db_user',
                    'db_name',
                    'version',
                    'password_type',
                    'status'
                ],
                'required',
                'message' => Yii::t('backend', 'Поле обязательно для заполнения')
            ],

            // числовые значения
            [['port', 'db_port', 'telnet_port', 'status'], 'integer'],

            // длина строк
            [
                [
                    'name',
                    'ip',
                    'db_host',
                    'db_user',
                    'db_pass',
                    'db_name',
                    'telnet_host',
                    'telnet_pass',
                    'version',
                    'password_type'
                ],
                'string',
                'max' => 255
            ],

            // авто-даты
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'name'          => Yii::t('backend', 'Название'),
            'ip'            => Yii::t('backend', 'IP'),
            'port'          => Yii::t('backend', 'Порт'),
            'db_host'       => Yii::t('backend', 'Хост БД'),
            'db_port'       => Yii::t('backend', 'Порт БД'),
            'db_user'       => Yii::t('backend', 'Пользователь БД'),
            'db_pass'       => Yii::t('backend', 'Пароль БД'),
            'db_name'       => Yii::t('backend', 'Название БД'),
            'telnet_host'   => Yii::t('backend', 'Telnet хост'),
            'telnet_port'   => Yii::t('backend', 'Telnet порт'),
            'telnet_pass'   => Yii::t('backend', 'Telnet пароль'),
            'version'       => Yii::t('backend', 'Версия'),
            'password_type' => Yii::t('backend', 'Тип шифрования'),
            'status'        => Yii::t('backend', 'Статус'),
            'created_at'    => Yii::t('backend', 'Дата создания'),
            'updated_at'    => Yii::t('backend', 'Дата изменения'),
        ];
    }

    public function getStatusList(): array
    {
        return [
            self::STATUS_ON  => Yii::t('backend', 'Включён'),
            self::STATUS_OFF => Yii::t('backend', 'Выключен'),
        ];
    }

    public function getPasswordTypeList(): array
    {
        return [
            'sha1'   => 'SHA1',
            'SHA256' => 'SHA256',
            'MD5'    => 'MD5',
            'plain'  => 'Plain',
        ];
    }

    public function isStatusOn(): bool
    {
        return (int)$this->status === self::STATUS_ON;
    }

    public function getStatusLabel(): string
    {
        return $this->getStatusList()[$this->status] ?? '-';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->updated_at = date('Y-m-d H:i:s');
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }
}
