<?php

namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

class Gs extends ActiveRecord
{
    public static function tableName()
    {
        return 'gs';
    }

    public function rules()
    {
        return [
            // обязательные поля
            [
                [
                    'name',
                    'ip',
                    'port',
                    'login_id',
                    'version',
                    'db_host',
                    'db_port',
                    'db_user',
                    'db_name',
                ],
                'required'
            ],

            // числовые
            [['port', 'db_port', 'login_id', 'status'], 'integer'],

            // строки
            [
                [
                    'name',
                    'ip',
                    'db_host',
                    'db_user',
                    'db_pass',
                    'db_name',
                    'version',
                ],
                'string',
                'max' => 255
            ],

            // значения по умолчанию
            [['status'], 'default', 'value' => 1],
            [['port'], 'default', 'value' => 3306],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t('backend', 'ID'),
            'name'                => Yii::t('backend', 'Название'),
            'ip'                  => Yii::t('backend', 'IP'),
            'port'                => Yii::t('backend', 'Порт'),
            'login_id'            => Yii::t('backend', 'Login-сервер'),
            'version'             => Yii::t('backend', 'Версия'),
            'db_host'             => Yii::t('backend', 'Хост БД'),
            'db_port'             => Yii::t('backend', 'Порт БД'),
            'db_user'             => Yii::t('backend', 'Пользователь БД'),
            'db_pass'             => Yii::t('backend', 'Пароль БД'),
            'db_name'             => Yii::t('backend', 'Название БД'),
            'status'              => Yii::t('backend', 'Статус'),
            'services_premium_cost' => Yii::t('backend', 'Стоимость премиум-услуги'),
        ];
    }

    public function isStatusOn()
    {
        return (int)$this->status === 1;
    }

    public function getStatus()
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public static function getStatusList()
    {
        return [
            0 => Yii::t('backend', 'Неактивен'),
            1 => Yii::t('backend', 'Активен'),
        ];
    }

    /**
     * Список доступных версий серверов
     */
    public static function getVersionList(): array
    {
        $path = Yii::getAlias('@app/../protected/modules/backend/components/versions');
        $files = glob($path . '/*.php');
        $versions = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                $versions[basename($file, '.php')] = basename($file, '.php');
            }
            ksort($versions);
        }
        return $versions;
    }

    /**
     * Геттер для services_premium_cost
     * Возвращает либо значение из поля, либо 0, если такого поля нет в таблице
     */
    public function getServicesPremiumCost()
    {
        // Если в таблице есть такое поле — вернёт его, иначе 0
        return $this->getAttribute('services_premium_cost') ?? 0;
    }
}
