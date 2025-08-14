<?php

namespace app\modules\backend\assets;

use yii\web\AssetBundle;

class BackendAsset extends AssetBundle
{
    public $depends = [
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}