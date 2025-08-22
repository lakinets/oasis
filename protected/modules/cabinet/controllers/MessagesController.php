<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\UserMessages;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class MessagesController extends CabinetBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UserMessages::find()
                ->where(['user_id' => \Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.user_messages_limit'] ?? 10,
            ],
        ]);

        return $this->render('index', [ 
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail($id)
    {
        $model = UserMessages::findOne(['id' => $id, 'user_id' => \Yii::$app->user->id]);
        if (!$model) {
            throw new NotFoundHttpException('Сообщение не найдено.');
        }

        if ($model->read == 0) {
            $model->read = 1;
            $model->save(false);
        }

        return $this->render('detail', ['model' => $model]);   // ✅ убрал "messages/"
    }
}