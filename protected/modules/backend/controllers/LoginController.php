<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;

class LoginController extends Controller
{
    public function actionIndex()
    {
        // Если пользователь уже вошел (и это админ) — пускаем в дашборд
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/backend/default/index']);
        }

        // Если это гость — жесткая переадресация на общий логин сайта
        return $this->redirect(['/login']);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Yii::$app->homeUrl);
    }
}