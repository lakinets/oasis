<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\News;

class NewsController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => News::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = News::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Новость не найдена');
        }

        return $this->render('view', ['model' => $model]);
    }
}
