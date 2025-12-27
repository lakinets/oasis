<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;   // ← НЕ BackendController
use app\modules\backend\models\LoginForm;

class LoginController extends Controller
{
    public $layout = '@app/modules/backend/views/layouts/login';

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {           // уже вошёл
            return $this->redirect(['/backend/default/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/backend/default/index']);
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/backend/login/index']);
    }
}