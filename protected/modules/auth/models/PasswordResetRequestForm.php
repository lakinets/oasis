<?php

namespace app\modules\auth\models;

use Yii;
use yii\base\Model;
use app\models\Users; // ← было backend\models\Users

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => Users::class,
                'message' => 'Такой e-mail не зарегистрирован.'
            ],
        ];
    }

    public function attributeLabels()
    {
        return ['email' => 'E-mail'];
    }

    /**
     * Отправляет письмо с токеном
     * @return bool
     */
    public function sendEmail()
    {
        /* @var $user Users */
        $user = Users::findOne(['email' => $this->email]);
        if (!$user) return false;

        $user->generatePasswordResetToken();
        if (!$user->save(false)) return false;

        return Yii::$app->mailer
            ->compose(
                ['html' => '@app/modules/auth/mails/reset-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['mail.sender'] => Yii::$app->params['mail.senderName']])
            ->setTo($this->email)
            ->setSubject('Сброс пароля на ' . Yii::$app->name)
            ->send();
    }
}