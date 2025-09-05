<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\captcha\CaptchaAction;
use app\models\forms\LoginForm;

class LoginController extends Controller
{
    public const MAX_FAILED_ATTEMPTS = 10;
    public const BLOCK_MINUTES       = 15;

    public function actions()
    {
        return [
            'captcha' => [
                'class'           => CaptchaAction::class,
				 'id'              => 'login',
                'height'          => 40,
                'width'           => 100,
                'minLength'       => 4,
                'maxLength'       => 5,
                'fixedVerifyCode' => YII_ENV === 'test' ? 'testme' : null,
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => \yii\filters\VerbFilter::class,
                'actions' => [
                    'captcha' => ['GET'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/cabinet/default/index']);
        }

        $model  = new LoginForm();
        $cache  = Yii::$app->cache;
        $ip     = Yii::$app->request->userIP;
        $failed = (int)$cache->get("login_failed_$ip");
        $blocked = ($failed >= self::MAX_FAILED_ATTEMPTS);

        if ($blocked) {
            Yii::$app->session->setFlash('error', "Вы временно заблокированы на " . self::BLOCK_MINUTES . " минут.");
        }

        if (Yii::$app->request->isPost && !$blocked) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login()) {
                $cache->delete("login_failed_$ip");
                return $this->redirect(['/cabinet/default/index']);
            }

            $failed++;
            $cache->set("login_failed_$ip", $failed, self::BLOCK_MINUTES * 60);
            Yii::$app->session->setFlash('error', "Неверный логин, пароль или код с картинки.");
        }

        return $this->render('index', [
            'model'       => $model,
            'blocked'     => $blocked,
            'failed'      => $failed,
            'maxAttempts' => self::MAX_FAILED_ATTEMPTS,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/']);
    }
}