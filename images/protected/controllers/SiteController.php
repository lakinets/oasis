<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Pages;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->renderPage('main');
    }

    public function actionView($page)
    {
        return $this->renderPage($page);
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
        return $this->render('error', ['exception' => new NotFoundHttpException('Unknown error.')]);
    }

    private function renderPage($page)
    {
        $model = Pages::find()
            ->where(['page' => $page, 'status' => 1])
            ->one();

        if (!$model) {
            Yii::$app->response->statusCode = 404;
        }

        return $this->render('page', [
            'model' => $model,
            'page' => $page,
            'notFound' => !$model,
        ]);
    }
}
