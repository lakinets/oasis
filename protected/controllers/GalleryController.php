<?php
namespace app\controllers;

use yii\web\Controller;

class GalleryController extends Controller
{
    public $layout = '@protected/views/layouts/master';

    public function actionIndex()
    {
        return $this->render('@protected/views/gallery/index');
    }
}