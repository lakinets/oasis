<?php
namespace app\controllers;

use yii\web\Controller;

class StatsController extends Controller
{
    public $layout = '@protected/views/layouts/master';

    public function actionIndex()
    {
        return $this->render('@protected/views/stats/index');
    }
}