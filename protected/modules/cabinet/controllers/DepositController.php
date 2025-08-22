<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\DepositForm;
use app\models\Transactions;

class DepositController extends CabinetBaseController
{
    public function actionIndex()
    {
        $model = new DepositForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $transaction = new Transactions([
                'user_id' => \Yii::$app->user->id,
                'sum' => $model->sum,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $transaction->save();
            \Yii::$app->session->set('transaction_id', $transaction->id);
            return $this->redirect(['deposit/processed']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionProcessed()
    {
        $id = \Yii::$app->session->get('transaction_id');
        $model = Transactions::findOne($id);
        if (!$model || $model->status != 0) {
            \Yii::$app->session->setFlash('error', 'Транзакция не найдена');
            return $this->redirect(['index']);
        }
        return $this->render('deposit/processed', ['model' => $model]);
    }
}