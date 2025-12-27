<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

use app\modules\backend\models\News;
use app\modules\backend\models\NewsSearch;

/**
 * Контроллер управления новостями.
 * Наследует BackendController, поэтому вход только для admin.
 */
class NewsController extends BackendController
{
    /* ---------- 1. Список новостей ---------- */
    public function actionIndex()
    {
        $searchModel  = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- 2. Добавление / редактирование ---------- */
    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new News();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $id
                ? Yii::t('backend', 'Новость сохранена.')
                : Yii::t('backend', 'Новость добавлена.')
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
        $model->status = News::STATUS_DELETED;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Новость удалена.'));
        return $this->redirect(['index']);
    }

    /* ---------- 5. Поиск модели ---------- */
    protected function findModel($id)
    {
        if (($model = News::find()
                ->andWhere(['!=', 'status', News::STATUS_DELETED])
                ->andWhere(['id' => $id])
                ->one()) === null) {
            throw new NotFoundHttpException('Новость не найдена.');
        }
        return $model;
    }
}