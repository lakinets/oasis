<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\BonusCodes;
use app\modules\cabinet\models\UserBonuses;
use yii\data\ActiveDataProvider;

class BonusesController extends CabinetBaseController
{
    public function actionIndex()
    {
        $bonusIds = \app\modules\cabinet\models\Bonuses::find()
            ->select('id')
            ->where(['status' => 1])
            ->column();

        $dataProvider = new ActiveDataProvider([
            'query' => UserBonuses::find()
                ->with(['bonusInfo.items'])
                ->where(['user_id' => \Yii::$app->user->id])
                ->andWhere(['bonus_id' => $bonusIds])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.bonuses.limit'] ?? 10,
            ],
        ]);

        return $this->render('index', [ // 'bonuses/'
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBonusCode()
    {
        $model = new BonusCodes(['scenario' => 'activated_code']);

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            // TODO: логика активации
            \Yii::$app->session->setFlash('success', 'Код активирован');
            return $this->refresh();
        }

        return $this->render('bonus-code', [ // 'bonuses/'
            'model' => $model,
        ]);
    }

    public function actionActivation($bonus_id)
    {
        // TODO: логика активации бонуса
        \Yii::$app->session->setFlash('success', 'Бонус активирован');
        return $this->redirect(['index']); // 'bonuses/'
    }
}