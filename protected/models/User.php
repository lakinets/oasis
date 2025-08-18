<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'users';
    }

    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($login)
    {
        return static::findOne(['login' => $login]);
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->auth_hash;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_hash === $authKey;
    }

    /**
     * Проверяет пароль:
     * - bcrypt (начинается с $2a$)
     * - plain (roottest)
     */
    public function validatePassword($password)
    {
        // 1. bcrypt
        if (strpos($this->password, '$2a$') === 0) {
            return \Yii::$app->security->validatePassword($password, $this->password);
        }

        // 2. plain text (roottest)
        return $this->password === $password;
    }

    /**
     * Устанавливает bcrypt-пароль
     */
    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }
}