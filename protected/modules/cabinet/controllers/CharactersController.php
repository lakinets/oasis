<?php

namespace app\modules\cabinet\controllers;

class CharactersController extends CabinetBaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($char_id)
    {
        return $this->render('view', ['char_id' => $char_id]);
    }

    public function actionTeleport($char_id)
    {
        // TODO: логика телепортации
        \Yii::$app->session->setFlash('success', 'Телепорт выполнен');
        return $this->redirect(['characters/index']);
    }
}