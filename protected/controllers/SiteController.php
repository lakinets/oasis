<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Cookie;
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
        return $this->render('error', [
            'exception' => new NotFoundHttpException(Yii::t('controllers', 'Неизвестная ошибка.')),
        ]);
    }

    /**
     * Страница входа
     */
    public function actionLogin()
    {
        $this->layout = 'main-public';
        $this->view->title = Yii::t('controllers', 'Вход в аккаунт');
        // Здесь подключи свою модель и логику
        return $this->render('login');
    }

    /**
     * Страница регистрации
     */
    public function actionRegister()
    {
        $this->layout = 'main-public';
        $this->view->title = Yii::t('controllers', 'Регистрация');
        // Здесь подключи свою модель и логику
        return $this->render('register');
    }

    /**
     * Восстановление пароля (опционально)
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-public';
        $this->view->title = Yii::t('controllers', 'Восстановление пароля');
        return $this->render('requestPasswordReset');
    }

    /**
     * Переключение языка интерфейса.
     * Вызывается из виджета LanguageSwitcher, сохраняет язык в сессии и cookie,
     * возвращает пользователя на ту же страницу, откуда он пришёл.
     */
    public function actionLanguage($lang)
    {
        $supported = array_keys(Yii::$app->params['languages'] ?? []);
        $referrer  = Yii::$app->request->referrer ?: Yii::$app->homeUrl;

        if (!in_array($lang, $supported, true)) {
            return $this->redirect($referrer);
        }

        Yii::$app->session->set('language', $lang);

        Yii::$app->response->cookies->add(new Cookie([
            'name'   => 'language',
            'value'  => $lang,
            'expire' => time() + 60 * 60 * 24 * 365,
        ]));

        return $this->redirect($referrer);
    }

    /**
     * Универсальный метод рендера страниц с поддержкой переводов.
     *
     * Берёт модель по slug'у, находит перевод под текущий язык
     * (с fallback на sourceLanguage), передаёт оба объекта во вьюху.
     */
    private function renderPage($page)
    {
        $model = Pages::find()
            ->where(['page' => $page, 'status' => Pages::STATUS_ON])
            ->one();

        if (!$model) {
            Yii::$app->response->statusCode = 404;

            return $this->render('page', [
                'model'       => null,
                'translation' => null,
                'page'        => $page,
                'notFound'    => true,
            ]);
        }

        $translation = $model->getTranslation();

        return $this->render('page', [
            'model'       => $model,
            'translation' => $translation,
            'page'        => $page,
            'notFound'    => false,
        ]);
    }
}