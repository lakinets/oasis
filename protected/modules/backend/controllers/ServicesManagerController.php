<?php
namespace app\modules\backend\controllers;

use Yii;
use app\models\Services;
use yii\web\NotFoundHttpException;

/**
 * ServicesManagerController — управление сервисами через GET-запросы
 * Наследует BackendController => доступ только для admin
 */
class ServicesManagerController extends BackendController
{
    public function actionIndex()
    {
        $services = Services::find()->orderBy(['id' => SORT_ASC])->all();
        return $this->render('index', ['services' => $services]);
    }

    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $cost = Yii::$app->request->get('cost');

        if (!$id || $cost === null) {
            Yii::$app->session->setFlash('error', 'Недостаточно параметров');
            return $this->redirect(['index']);
        }

        $service = $this->findModel($id);
        $service->cost = (float)$cost;

        if ($service->save()) {
            Yii::$app->session->setFlash('success', "Стоимость сервиса '{$service->name}' обновлена до {$cost}");
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при обновлении стоимости');
        }

        return $this->redirect(['index']);
    }

    public function actionToggle()
    {
        $id = Yii::$app->request->get('id');
        if (!$id) {
            Yii::$app->session->setFlash('error', 'Не указан ID сервиса');
            return $this->redirect(['index']);
        }

        $service = $this->findModel($id);
        $service->status = $service->status == Services::STATUS_ENABLED ? Services::STATUS_DISABLED : Services::STATUS_ENABLED;

        if ($service->save()) {
            $newStatusLabel = $service->status == Services::STATUS_ENABLED ? 'включен' : 'выключен';
            Yii::$app->session->setFlash('success', "Сервис '{$service->name}' {$newStatusLabel}");
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при изменении статуса');
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Services::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Сервис не найден.');
    }
}