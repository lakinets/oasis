<?php

namespace app\modules\cabinet\controllers;

class ServicesController extends CabinetBaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPremium()
    {
        return $this->render('premium');
    }

    public function actionRemoveHwid()
    {
        return $this->render('remove-hwid');
    }

    public function actionChangeCharName()
    {
        return $this->render('change-char-name');
    }

    public function actionChangeGender()
    {
        return $this->render('change-gender');
    }
}