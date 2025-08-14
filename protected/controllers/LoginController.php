<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;

class LoginController extends Controller
{
    public function actionIndex()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
