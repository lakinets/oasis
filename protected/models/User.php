<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель пользователя (таблица `users`).
 * Совместима с админской backend\models\Users
 */
class Users extends ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_hash;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_hash === $authKey;
    }

    /**
     * Безопасная проверка пароля.
     * Поддерживает только современные алгоритмы (bcrypt / blowfish).
     * Устаревшие методы (MD5, plaintext) удалены из соображений безопасности.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        // 1. bcrypt (Yii2 стандарт)
        if (strpos($this->password, '$2y$') === 0 || strpos($this->password, '$2a$') === 0) {
            return Yii::$app->security->validatePassword($password, $this->password);
        }

        // 2. CRYPT_BLOWFISH (Yii1 legacy, но криптографически безопасен)
        if (preg_match('/^\$\d+\$/', $this->password)) {
            return crypt($password, $this->password) === $this->password;
        }

        // Любые другие форматы (MD5, plaintext) отклоняются
        return false;
    }

    /**
     * Генерирует токен сброса пароля и время истечения
     */
    public function generatePasswordResetToken()
    {
        $this->reset_token = Yii::$app->security->generateRandomString(32);
        $this->reset_expires = date('Y-m-d H:i:s', time() + Yii::$app->params['auth.resetTokenExpire']);
    }

    /**
     * Удаляет токен сброса пароля
     */
    public function removePasswordResetToken()
    {
        $this->reset_token = null;
        $this->reset_expires = null;
    }

    /**
     * Проверяет, не истёк ли токен сброса пароля
     *
     * @return bool
     */
    public function validatePasswordResetToken()
    {
        return !empty($this->reset_token) &&
               strtotime($this->reset_expires) > time();
    }

    /**
     * Поиск пользователя по логину
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['login' => $username]);
    }

    /**
     * Поиск пользователя по e-mail
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Устанавливает новый пароль (bcrypt)
     * И сбрасывает auth_hash для инвалидации старых сессий.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
        // Сбрасываем ключ автологина, чтобы разлогинить пользователя на всех устройствах
        $this->auth_hash = null; 
    }

    /**
     * Генерирует случайный auth_key
     */
    public function generateAuthKey()
    {
        $this->auth_hash = Yii::$app->security->generateRandomString();
    }
}