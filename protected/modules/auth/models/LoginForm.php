<?php

namespace app\modules\auth\models;

use Yii;
use yii\base\Model;
use yii\captcha\CaptchaValidator;
use app\helpers\Config;   // подключаем хелпер

class LoginForm extends Model
{
    public $login;
    public $password;
    public $verifyCode;

    private $_user = false;

    public function rules()
    {
        $rules = [
            [['login', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];

        // <-- добавляем капчу ТОЛЬКО если включено в настройках -->
        if (Config::isOn('login.captcha.allow')) {
            $rules[] = ['verifyCode', 'required'];
            $rules[] = ['verifyCode', CaptchaValidator::class, 'captchaAction' => '/auth/default/captcha'];
        }

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
            'verifyCode' => 'Код с картинки',
        ];
    }

    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный логин или пароль.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
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