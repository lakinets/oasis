<?php
namespace app\modules\auth\models;

use yii\base\Model;
use app\modules\backend\models\Users;

class ResetPasswordForm extends Model
{
    public $password;
    public $passwordRepeat;

    private $_user;

    public function __construct(Users $user, $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['password', 'passwordRepeat'], 'required'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password',
             'message' => 'Пароли не совпадают.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Новый пароль',
            'passwordRepeat' => 'Повтор пароля',
        ];
    }

    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);   // ваш существующий метод
        $user->removePasswordResetToken();
        return $user->save(false);
    }
}