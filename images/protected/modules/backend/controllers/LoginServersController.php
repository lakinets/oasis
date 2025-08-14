<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

use app\modules\backend\models\LoginServers;
use app\modules\backend\models\LoginServersSearch;

class LoginServersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules'  => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    /* ---------- 1. Список серверов ---------- */
    public function actionIndex()
    {
        $searchModel  = new LoginServersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- 2. Добавление / редактирование ---------- */
    public function actionForm($ls_id = null)
    {
        $model = $ls_id ? $this->findModel($ls_id) : new LoginServers();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $ls_id
                ? Yii::t('backend', 'Изменения сохранены.')
                : Yii::t('backend', 'Логин добавлен.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('form', ['model' => $model]);
    }

    /* ---------- 3. Переключение статуса ---------- */
    public function actionAllow($ls_id)
    {
        $model = $this->findModel($ls_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['index']);
    }

    /* ---------- 4. Удаление (soft delete) ---------- */
    public function actionDel($ls_id)
    {
        $model = $this->findModel($ls_id);
        $model->status = LoginServers::STATUS_DELETED;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Логин сервер <b>:name</b> удалён', [
            ':name' => htmlspecialchars($model->name)
        ]));
        return $this->redirect(['index']);
    }

    /* ---------- 5. Список аккаунтов на LS ---------- */
    public function actionAccounts($ls_id)
    {
        $perPage = 20;

        try {
            // Пример: подключение через сервис-обёртку
            $ls  = $this->findModel($ls_id);
            $l2  = Yii::$app->l2->connect($ls_id); // ваш объект-помощник

            $accounts = $l2->accounts()->queryAll();
            $provider = new ArrayDataProvider([
                'allModels'  => $accounts,
                'key'        => 'login',
                'pagination' => ['pageSize' => $perPage],
                'sort'       => ['attributes' => ['login']],
            ]);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'LoginServersController');
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }

        return $this->render('accounts/index', [
            'ls'           => $ls,
            'dataProvider' => $provider,
            'perPage'      => $perPage,
        ]);
    }

    /* ---------- 6. Поиск модели ---------- */
    protected function findModel($id)
    {
        if (($model = LoginServers::find()->andWhere(['!=', 'status', LoginServers::STATUS_DELETED])->andWhere(['id' => $id])->one()) === null) {
            throw new NotFoundHttpException('Сервер не найден.');
        }
        return $model;
    }
}