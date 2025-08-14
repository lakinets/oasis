<?php
namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';
    public $css = [
        'views/assets/css/main.css',
    ];
    public $js = [
        'views/assets/js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\Bootstrap3Asset', // ← подключаем Bootstrap3Asset
    ];
}
