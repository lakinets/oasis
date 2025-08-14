<?php

namespace app\modules\stats;

use yii\base\Module;

class StatsModule extends Module
{
    public $controllerNamespace = 'app\modules\stats\controllers';

    public function init()
    {
        parent::init();
        // кастомная инициализация (если нужно)
    }
}
