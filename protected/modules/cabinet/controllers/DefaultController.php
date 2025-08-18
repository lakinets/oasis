<?php

namespace app\modules\cabinet\controllers;

class DefaultController extends CabinetBaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['/']);
    }
}