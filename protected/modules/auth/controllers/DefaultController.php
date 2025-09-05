<?php

namespace app\modules\auth\controllers;

use Yii;
use yii\web\Controller;
use yii\captcha\CaptchaAction;
use app\modules\auth\models\LoginForm;
use app\modules\auth\models\PasswordResetRequestForm;
use app\modules\auth\models\ResetPasswordForm;
use app\models\Users; // ← фронтовая модель

class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/cabinet/default/index']);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /* ---------- запрос токена ---------- */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте e-mail.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Не удалось отправить письмо.');
        }

        return $this->render('requestPasswordReset', ['model' => $model]);
    }

    /* ---------- собственно сброс ---------- */
    public function actionResetPassword($token)
    {
        if (empty($token) || strlen($token) < 20) {
            throw new \yii\web\BadRequestHttpException('Неверный токен.');
        }

        $user = Users::findOne(['reset_token' => $token]);
        if (!$user || !$user->validatePasswordResetToken()) {
            throw new \yii\web\BadRequestHttpException('Токен просрочен или неверен.');
        }

        $model = new ResetPasswordForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'Новый пароль сохранён.');
                return $this->redirect(['/auth/default/login']);
            }
        }

        return $this->render('resetPassword', ['model' => $model]);
    }
}