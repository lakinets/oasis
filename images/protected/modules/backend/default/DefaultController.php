<?php
namespace app\modules\backend\controllers;

use yii\web\Controller;
use app\modules\backend\models\LoginForm;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        // Статус модуля из конфига
        $enabled = \Yii::$app->params['backend_enabled'] ?? true;

        if (!$enabled) {
            return $this->render('disabled');
        }

        // если залогинен — показываем панель
        if (!\Yii::$app->user->isGuest) {
            return $this->render('dashboard');
        }

        // иначе форма входа
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->refresh();
        }

        return $this->render('login-form', ['model' => $model]);
    }
}