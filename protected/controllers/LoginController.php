<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\forms\LoginForm;

class LoginController extends Controller
{
    public const MAX_FAILED_ATTEMPTS = 10;
    public const BLOCK_MINUTES       = 15;

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/cabinet/default/index']);
        }

        $model = new LoginForm();

        $cacheKey = 'login_failed_' . Yii::$app->request->userIP;
        $failed   = (int)Yii::$app->cache->get($cacheKey);
        $blocked  = ($failed >= self::MAX_FAILED_ATTEMPTS);

        if ($blocked) {
            Yii::$app->session->setFlash('error', "Вы временно заблокированы на " . self::BLOCK_MINUTES . " минут.");
        }

        if (Yii::$app->request->isPost && !$blocked) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                Yii::$app->cache->delete($cacheKey);
                return $this->redirect(['/cabinet/default/index']);
            }

            $failed++;
            Yii::$app->cache->set($cacheKey, $failed, self::BLOCK_MINUTES * 60);
            Yii::$app->session->setFlash('error', "Неверный логин или пароль.");
        }

        return $this->render('index', [
            'model'       => $model,
            'blocked'     => $blocked,
            'failed'      => $failed,
            'maxAttempts' => self::MAX_FAILED_ATTEMPTS,
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->redirect(['/']);
    }
}