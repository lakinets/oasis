<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Базовый контроллер backend-модуля.
 * Доступ разрешён только если в таблице `users` поле role = 'admin'.
 * Гостя автоматически ведёт на форму входа.
 */
class BackendController extends Controller
{
    public $layout = 'master';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            /* @var $identity \app\models\User */
                            $identity = Yii::$app->user->identity;
                            return $identity->role === 'admin';
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/backend/login']);
                    }
                    throw new ForbiddenHttpException('Доступ запрещён');
                },
            ],
        ];
    }
}