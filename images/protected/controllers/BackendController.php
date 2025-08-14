<?php
namespace app\controllers;

use yii\web\Controller;

class BackendController extends Controller
{
    public $layout = '@protected/views/layouts/master';

    public function actionIndex()
    {
        return $this->render('@protected/views/backend/default/index');
    }
}