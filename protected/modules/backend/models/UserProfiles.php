<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

/**
 * Модель профиля пользователя (таблица `user_profiles`).
 */
class UserProfiles extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_profiles';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['balance', 'vote_balance'], 'number', 'min' => 0],
            [['phone'], 'string', 'max' => 54],
            [['protected_ip'], 'string'],
            [['preferred_language'], 'string', 'max' => 2],
        ];
    }

    /**
     * Связь «один-к-одному» с пользователем.
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

    /**
     * Возвращает массив IP-адресов из строки.
     */
    public function getProtectedIps()
    {
        return $this->protected_ip ? explode("\n", $this->protected_ip) : [];
    }

    /**
     * Устанавливает IP-адреса из массива в строку.
     */
    public function setProtectedIps(array $ips)
    {
        $this->protected_ip = implode("\n", array_filter(array_map('trim', $ips)));
    }
}