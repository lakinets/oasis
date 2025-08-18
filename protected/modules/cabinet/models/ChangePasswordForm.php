<?php

namespace app\modules\cabinet\models;

use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $old_password;
    public $new_password;

    public function rules()
    {
        return [
            [['old_password', 'new_password'], 'required'],
            ['new_password', 'string', 'min' => 6],
            ['old_password', 'validateOldPassword'],
        ];
    }

    public function validateOldPassword($attribute)
    {
        if (!\Yii::$app->security->validatePassword($this->old_password, \Yii::$app->user->identity->password_hash)) {
            $this->addError($attribute, 'Неверный старый пароль');
        }
    }
}