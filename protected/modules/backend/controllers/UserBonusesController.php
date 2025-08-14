<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\backend\models\UserBonuses;
use app\modules\backend\models\UserBonusesSearch;

class UserBonusesController extends Controller
{
    public function actionIndex()
    {
        $searchModel  = new UserBonusesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionForm($id = null)
    {
        $model = $id ? $this->findModel($id) : new UserBonuses();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Сохранено');
            return $this->redirect(['index']);
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionAllow($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false);
        return $this->redirect(['index']);
    }

    public function actionDel($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = UserBonuses::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Не найдено');
    }
}
