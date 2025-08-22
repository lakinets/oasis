<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\backend\models\LoginServers;
use app\modules\backend\models\LoginServersSearch;

class LoginServersController extends BackendController
{
    /**
     * Разрешаем GET и POST для удаления внутри админки
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'del' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * Список логин-серверов
     */
    public function actionIndex()
    {
        $searchModel  = new LoginServersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Форма добавления/редактирования
     */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new LoginServers();

        $versionsDir = Yii::getAlias('@app/../protected/l2j');
        $versions    = [];

        if (is_dir($versionsDir)) {
            foreach (scandir($versionsDir) as $file) {
                $path = $versionsDir . DIRECTORY_SEPARATOR . $file;
                if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                    $key = pathinfo($file, PATHINFO_FILENAME);
                    $versions[$key] = $key;
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $id
                    ? Yii::t('backend', 'Изменения сохранены.')
                    : Yii::t('backend', 'Логин-сервер добавлен.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model'    => $model,
            'versions' => $versions,
        ]);
    }

    /**
     * Переключение статуса
     */
    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == LoginServers::STATUS_ON
            ? LoginServers::STATUS_OFF
            : LoginServers::STATUS_ON;
        $model->save(false);

        Yii::$app->session->setFlash(
            'success',
            $model->status
                ? Yii::t('backend', 'Сервер включён.')
                : Yii::t('backend', 'Сервер выключен.')
        );
        return $this->redirect(['index']);
    }

    /**
     * Удаление сервера (GET и POST)
     */
    public function actionDel($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Сервер удалён.'));
        return $this->redirect(['index']);
    }

    /**
     * Поиск модели по ID
     */
    protected function findModel($id): LoginServers
    {
        if (($model = LoginServers::findOne((int)$id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('backend', 'Сервер не найден.'));
    }
}