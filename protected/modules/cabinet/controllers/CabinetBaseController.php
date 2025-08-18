<?php

namespace app\modules\cabinet\controllers;

use Yii;
use yii\web\Controller;

class CabinetBaseController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        return parent::beforeAction($action);
    }
}