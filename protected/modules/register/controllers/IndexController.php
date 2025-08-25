<?php
namespace app\modules\register\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\register\models\RegisterForm;
use app\components\AppConfig;

class IndexController extends Controller
{
    public $layout = '@app/views/layouts/main';

    public function actionIndex()
    {
        if (!AppConfig::registerEnabled()) {
            throw new NotFoundHttpException('Регистрация отключена.');
        }

        $model = new RegisterForm();

        if (!$model->gs_list) {
            throw new NotFoundHttpException('Регистрация невозможна из-за отсутствия серверов.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $model->registerAccount();
                Yii::$app->session->setFlash('success', 'Регистрация прошла успешно!');
                return $this->refresh();
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', 'Ошибка регистрации: ' . $e->getMessage());
            }
        }

        return $this->render('index', [
            'model'           => $model,
            'prefixesEnabled' => AppConfig::prefixesEnabled(),
            'captchaEnabled'  => AppConfig::captchaEnabled(),
            'referralsEnabled'=> AppConfig::referralsEnabled(),
        ]);
    }
}