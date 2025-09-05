<?php
namespace app\models\forms;

use yii\base\Model;
use yii\captcha\CaptchaValidator;
use app\models\Users;
use app\components\AppConfig;

class LoginForm extends Model
{
    public $login;
    public $password;
    public $rememberMe = true;
    public $verifyCode;

    private $_user = null;

    public function rules()
    {
        $rules = [
            [['login', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];

        // Капча управляется настройками
        if (AppConfig::captchaEnabled()) {
            $rules[] = ['verifyCode', 'required'];
            // ВАЖНО: абсолютный маршрут, чтобы совпадал ключ сессии капчи
            $rules[] = ['verifyCode', CaptchaValidator::class, 'captchaAction' => '/login/captcha'];
        }

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'login'      => 'Игровой логин',
            'password'   => 'Пароль',
            'rememberMe' => 'Запомнить меня',
            'verifyCode' => 'Код с картинки',
        ];
    }

    public function validatePassword($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Неверный логин или пароль.');
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return \Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0
            );
        }
        return false;
    }

    public function getUser()
    {
        if ($this->_user === null) {
            // Аналогично админке: ищем по колонке login
            $this->_user = Users::findByUsername($this->login);
        }
        return $this->_user;
    }
}
