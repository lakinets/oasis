<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Pages;

class SiteController extends Controller
{
    /**
     * Главная страница
     */
    public function actionIndex()
    {
        return $this->renderPage('main');
    }

    /**
     * Просмотр произвольной страницы
     */
    public function actionView($page)
    {
        return $this->renderPage($page);
    }

    /**
     * Страница ошибки
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
        return $this->render('error', ['exception' => new NotFoundHttpException('Unknown error.')]);
    }

    /**
     * Страница входа
     */
    public function actionLogin()
    {
        $this->layout = 'main-public';
        $this->view->title = 'Вход в аккаунт';
        // Здесь подключи свою модель и логику
        return $this->render('login');
    }

    /**
     * Страница регистрации
     */
    public function actionRegister()
    {
        $this->layout = 'main-public';
        $this->view->title = 'Регистрация';
        // Здесь подключи свою модель и логику
        return $this->render('register');
    }

    /**
     * Восстановление пароля (опционально)
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-public';
        $this->view->title = 'Восстановление пароля';
        return $this->render('requestPasswordReset');
    }

    /**
     * Универсальный метод рендера страниц
     */
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