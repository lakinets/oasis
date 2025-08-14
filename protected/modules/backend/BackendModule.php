<?php
namespace app\modules\backend;

use Yii;
use yii\base\Module;

class BackendModule extends Module
{
    public $controllerNamespace = 'app\modules\backend\controllers';

    public function init()
    {
        parent::init();

        Yii::setAlias('@backendModels', '@app/modules/backend/models');
        Yii::setAlias('@backendComponents', '@app/modules/backend/components');

        $this->layout = '@app/modules/backend/views/layouts/main';
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $controller = $action->controller->id;
        $actionId = $action->id;

        if (Yii::$app->user->isGuest && !($controller === 'login' && in_array($actionId, ['index', 'logout']))) {
            Yii::$app->response->redirect(['/backend/login/index'])->send();
            return false;
        }

        return true;
    }
}
