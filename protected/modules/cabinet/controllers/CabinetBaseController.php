<?php

namespace app\modules\cabinet\controllers;

use Yii;
use yii\web\Controller;
use app\models\Gs;

class CabinetBaseController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        // Если включен режим "Пополнение" - блокируем доступ к Услугам и Депозиту
        $controllerId = $action->controller->id;
        if (in_array($controllerId, ['services', 'deposit'], true) && Gs::isTopupMode()) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Этот раздел недоступен в текущем режиме работы сервера.'));
            return $this->redirect(['/cabinet']);
        }

        return parent::beforeAction($action);
    }
}