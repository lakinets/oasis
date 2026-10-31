<?php

namespace app\modules\auth\models;

use Yii;
use yii\base\Model;
use yii\captcha\CaptchaValidator;
use app\helpers\Config;

class LoginForm extends Model
{
    public $login;
    public $password;
    public $verifyCode;

    private $_user = false;

    // Константы для защиты от брутфорса
    const MAX_ATTEMPTS = 5;
    const BLOCK_TIME = 900; // 15 минут (900 секунд)

    public function rules()
    {
        $rules = [
            [['login', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];

        if (Config::isOn('login.captcha.allow')) {
            $rules[] = ['verifyCode', 'required'];
            $rules[] = ['verifyCode', CaptchaValidator::class, 'captchaAction' => '/auth/default/captcha'];
        }

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'login' => Yii::t('views', 'Логин'),
            'password' => Yii::t('views', 'Пароль'),
            'verifyCode' => Yii::t('views', 'Код с картинки'),
        ];
    }

    /**
     * Проверка на блокировку IP
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $ip = Yii::$app->request->userIP;
        $cacheKey = 'login_attempts_' . $ip;
        $attempts = (int)Yii::$app->cache->get($cacheKey);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->addError('password', Yii::t('views', 'Слишком много неудачных попыток. Попробуйте через 15 минут.'));
            return false;
        }

        return true;
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                // Увеличиваем счетчик неудачных попыток
                $ip = Yii::$app->request->userIP;
                $cacheKey = 'login_attempts_' . $ip;
                $attempts = (int)Yii::$app->cache->get($cacheKey);
                Yii::$app->cache->set($cacheKey, $attempts + 1, self::BLOCK_TIME);

                $this->addError($attribute, Yii::t('views', 'Неверный логин или пароль.'));
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            // Очищаем счетчик при успешном входе
            $ip = Yii::$app->request->userIP;
            Yii::$app->cache->delete('login_attempts_' . $ip);

            return Yii::$app->user->login($this->getUser());
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = \app\modules\backend\models\Users::findOne(['login' => $this->login]);
        }
        return $this->_user;
    }
}