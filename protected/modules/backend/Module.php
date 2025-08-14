<?php
namespace app\modules\backend;

use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    // Пространство имён для контроллеров модуля
    public $controllerNamespace = 'app\modules\backend\controllers';

    public function init()
    {
        parent::init();

        // Здесь можно добавлять инициализацию модуля, например, компоненты, настройки и т.п.
    }
}

