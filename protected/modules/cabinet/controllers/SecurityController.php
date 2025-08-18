<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\UserProfiles;

class SecurityController extends CabinetBaseController
{
    public function actionIndex()
    {
        $model = UserProfiles::findOne(['user_id' => \Yii::$app->user->id]) ?? new UserProfiles(['user_id' => \Yii::$app->user->id]);
        $model->setScenario('security');

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Данные сохранены');
            return $this->refresh();
        }

        return $this->render('security/index', ['model' => $model]);
    }
}