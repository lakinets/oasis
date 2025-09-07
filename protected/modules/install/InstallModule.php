<?php
namespace app\modules\install;

use Yii;

class InstallModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\install\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->view->theme = null;          // отключаем тему
        $this->layout = 'main';                  // свой layout
        $this->layoutPath = '@app/modules/install/views/layouts';
    }
}