<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\modules\backend\models\Users;

class LoginForm extends Model
{
    public $login    = '';
    public $password = '';
    public $rememberMe = true;

    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Неверный логин или пароль.');
        }
    }

    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 7 : 0);
    }

    protected function getUser()
    {
        return Users::findOne(['login' => $this->login]);
    }
}