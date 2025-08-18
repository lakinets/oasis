<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\UsersAuthLogs;
use yii\data\ActiveDataProvider;

class AuthHistoryController extends CabinetBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UsersAuthLogs::find()
                ->where(['user_id' => \Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.auth_logs_limit'] ?? 20,
            ],
        ]);

        return $this->render('auth-history/index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}