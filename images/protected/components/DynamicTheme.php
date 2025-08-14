<?php
namespace app\components;

use yii\base\Component;
use app\models\Config;

class DynamicTheme extends Component
{
public function init()
{
    // 1. backend-модуль не должен использовать тему
    if (strpos(\Yii::$app->requestedRoute, 'backend/') === 0) {
        return;   // тема вообще не подключается
    }

    // 2. для всего остального работаем как раньше
    $themeName = Config::theme();        // читаем из БД
    $basePath  = '@app/themes/' . $themeName;

    if (!is_dir(\Yii::getAlias($basePath))) {
        $themeName = 'ghtweb';           // fallback на ghtweb
        $basePath  = '@app/themes/ghtweb';
    }

    \Yii::$app->view->theme = \Yii::createObject([
        'class'    => 'yii\base\Theme',
        'basePath' => $basePath,
        'baseUrl'  => $basePath,
    ]);
}
}