<?php
namespace app\assets;

use yii\web\AssetBundle;

class Bootstrap3Asset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap/dist'; // указывает на папку Bootstrap в vendor
    public $css = [
        'css/bootstrap.min.css',
    ];
    public $js = [
        'js/bootstrap.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset', // Bootstrap зависит от jQuery
    ];
}