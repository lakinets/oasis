<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * Универсальный просмотр «псевдо-страницы» в админке.
     * Не путать с фронтовым app\controllers\SiteController.
     *
     * @param string $page
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($page)
    {
        // Простейшая защита от path traversal — только буквы, цифры, дефис и подчёркивание.
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $page)) {
            throw new NotFoundHttpException(Yii::t('backend', 'Страница не найдена'));
        }

        return $this->render('view', ['page' => $page]);
    }
}