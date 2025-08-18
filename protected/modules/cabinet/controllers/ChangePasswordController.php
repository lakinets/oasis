<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\ChangePasswordForm;

class ChangePasswordController extends CabinetBaseController
{
    public function actionIndex()
    {
        $model = new ChangePasswordForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            // TODO: смена пароля
            \Yii::$app->session->setFlash('success', 'Пароль изменён');
            return $this->refresh();
        }

        return $this->render('change-password/index', [
            'model' => $model,
        ]);
    }
}