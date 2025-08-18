<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\ReferalsProfit;
use yii\data\ActiveDataProvider;

class ReferalsController extends CabinetBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ReferalsProfit::find()
                ->where(['referer_id' => \Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.referals.limit'] ?? 10,
            ],
        ]);

        return $this->render('referals/index', [
            'dataProvider' => $dataProvider,
            'countReferals' => \app\models\Referals::find()
                ->where(['referer' => \Yii::$app->user->id])
                ->count(),
        ]);
    }
}