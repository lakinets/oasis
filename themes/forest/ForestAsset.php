<?php
namespace app\themes\forest;

use yii\web\AssetBundle;

class ForestAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/forest/assets';
    public $baseUrl  = '@web/themes/forest/assets';

    public $css = [
        'css/style.css',
        'owl/owl.carousel.css',
    ];

    public $js = [
        'js/jquery-1.9.1.min.js',
        'owl/owl.carousel.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}