<?php
namespace app\controllers\backend;

use yii\web\Controller;

class DefaultController extends Controller
{
    public $layout = '@protected/views/layouts/master';

    public function actionIndex()
    {
        return $this->render('@protected/views/backend/default/index');
    }
}