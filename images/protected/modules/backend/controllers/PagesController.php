<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use app\modules\backend\models\Pages;
use app\modules\backend\models\PagesSearch;

class PagesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    /* ---------- 1. Список страниц ---------- */
    public function actionIndex()
    {
        $searchModel  = new PagesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- 2. Добавление / редактирование ---------- */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new Pages();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $id
                ? Yii::t('backend', 'Страница сохранена.')
                : Yii::t('backend', 'Страница добавлена.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('form', ['model' => $model]);
    }

    /* ---------- 3. Переключение статуса ---------- */
    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['index']);
    }

    /* ---------- 4. Удаление (soft delete) ---------- */
    public function actionDel($id)
    {
        $model = $this->findModel($id);
        $model->status = Pages::STATUS_DELETED;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Страница удалена.'));
        return $this->redirect(['index']);
    }

    /* ---------- 5. Поиск модели ---------- */
    protected function findModel($id)
    {
        if (($model = Pages::find()->andWhere(['status' => [1, 0]])->andWhere(['id' => $id])->one()) === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }
        return $model;
    }
}