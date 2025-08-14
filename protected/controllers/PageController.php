<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{
    // Используем layout из темы, а не жёстко заданный в protected
    public $layout = '@themes/views/layouts/main';

    public function actionIndex($page_name = 'about')
    {
        $viewFile = "@app/views/page/{$page_name}.php";
        $resolvedPath = Yii::getAlias($viewFile);

        if (!file_exists($resolvedPath)) {
            throw new NotFoundHttpException('Страница не найдена');
        }

        return $this->render("/page/{$page_name}");
    }

    public function actionAbout()
    {
        return $this->render('/page/about');
    }
}
