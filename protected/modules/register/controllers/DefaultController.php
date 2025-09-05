<?php
namespace app\modules\register\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\captcha\CaptchaAction;
use app\modules\register\models\RegisterForm;
use app\components\AppConfig;
use app\modules\register\services\RegistrationService;

class DefaultController extends Controller
{
    public $layout = '@app/views/layouts/main';

    public function actions()
    {
        return [
            'captcha' => [
                'class'           => CaptchaAction::class,
                'height'          => 40,
                'width'           => 100,
                'minLength'       => 3,
                'maxLength'       => 6,
                'fixedVerifyCode' => YII_ENV === 'test' ? 'testme' : null,
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => \yii\filters\VerbFilter::class,
                'actions' => ['captcha' => ['GET']],
            ],
        ];
    }

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

    public function actionActivated($hash)
    {
        $cache = Yii::$app->cache;
        $data  = $cache->get('registerActivated' . $hash);
        $cache->delete('registerActivated' . $hash);

        if ($data === false) {
            Yii::$app->session->setFlash('error', 'Ключ для активации аккаунта не найден.');
            return $this->redirect(['index']);
        }

        $user = \app\models\User::findOne($data['user_id']);
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Аккаунт не найден.');
            return $this->redirect(['index']);
        }
        if ((int)$user->activated === 1) {
            Yii::$app->session->setFlash('error', 'Аккаунт уже активирован.');
            return $this->redirect(['index']);
        }

        try {
            (new RegistrationService())->createLsAccount($user->ls_id, $user->login, $data['password']);
            $user->activated = 1;
            $user->save(false);
            Yii::$app->session->setFlash('success', 'Активация аккаунта прошла успешно. Приятной игры!');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при активации: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}