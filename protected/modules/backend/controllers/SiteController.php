<?php

namespace app\modules\backend\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public function actionView($page)
    {
        return $this->render('view', ['page' => $page]);
    }
}
