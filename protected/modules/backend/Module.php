<?php

namespace app\modules\backend;

use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = 'app\modules\backend\controllers';

    public function init()
    {
        parent::init();

        // Отдельный компонент user для backend-модуля
        Yii::$app->set('user', [
            'class'           => \yii\web\User::class,
            'identityClass'   => \app\modules\backend\models\Users::class,
            'enableAutoLogin' => true,
            'loginUrl'        => ['/backend/default/login'],
        ]);
    }
}