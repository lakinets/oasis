<?php
// 1. Максимальное отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// 2. Минимальный Yii-запуск
require_once __DIR__ . '/framework/yii.php';

$config = [
    'basePath' => __DIR__ . '/protected',
    'components' => [
        'db' => [
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=l2web',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
    ],
];

class SiteController extends CController
{
    public function actionIndex()
    {
        echo '<h1>Hello GHTWEB 5 – debug OK</h1>';
    }
}

try {
    Yii::createWebApplication($config)->run();
} catch (Throwable $e) {
    echo '<pre>';
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
    echo '</pre>';
}