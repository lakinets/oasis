<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель пользователя (таблица `users`).
 * Данные профиля (balance, vote_balance, phone, protected_ip) хранятся в `user_profiles`.
 */
class Users extends ActiveRecord implements IdentityInterface
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER  = 'user';

    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['login', 'email', 'role', 'activated'], 'required'],
            [['activated'], 'integer'],
            [['role'], 'string', 'max' => 24],
            [['login', 'email'], 'string', 'max' => 255],
        ];
    }

    public static function getRoleList()
    {
        return [
            self::ROLE_ADMIN => 'Админ',
            self::ROLE_USER  => 'Пользователь',
        ];
    }

    public static function getActivatedStatusList()
    {
        return [
            0 => 'Не активен',
            1 => 'Активен',
        ];
    }

    /**
     * Связь «один-к-одному» с профилем пользователя.
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfiles::class, ['user_id' => 'user_id']);
    }

    /**
     * Возвращает текстовое описание роли.
     */
    public function getRoleLabel()
    {
        return static::getRoleList()[$this->role] ?? $this->role;
    }

    /* === IdentityInterface === */

    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // не используем
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        return $this->auth_hash; // или null, если не используется
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_hash === $authKey;
    }

    /**
     * Проверяет пароль:
     * 1. bcrypt (если password начинается с $2$)
     * 2. MD5 legacy fallback (если password = md5)
     * 3. plain text fallback (roottest)
     */
    public function validatePassword($password)
    {
        // 1. bcrypt
        if (strpos($this->password, '$2') === 0) {
            return \Yii::$app->security->validatePassword($password, $this->password);
        }

        // 2. MD5 (legacy)
        if ($this->password === md5($password)) {
            return true;
        }

        // 3. plain text (fallback)
        return $this->password === $password;
    }

    /**
     * Устанавливает bcrypt-пароль (рекомендуемый способ).
     */
    public function setPassword($password)
    {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }
}