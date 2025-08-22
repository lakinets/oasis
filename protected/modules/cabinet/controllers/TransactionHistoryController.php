<?php

namespace app\modules\cabinet\controllers;

use app\models\Transactions;
use yii\data\ActiveDataProvider;

class TransactionHistoryController extends CabinetBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Transactions::find()
                ->where(['user_id' => \Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.transaction_history.limit'] ?? 20,
            ],
        ]);

        return $this->render('index', [ 
            'dataProvider' => $dataProvider,
        ]);
    }
}