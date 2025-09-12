<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

// 🔹 Проверяем, есть ли установщик и что мы НЕ на install
$installPath = __DIR__ . '/protected/modules/install';

// Текущий URI (без параметров)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (is_dir($installPath) && strpos($requestUri, '/install') !== 0) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $url = $protocol . "://" . $host . "/install";
    header("Location: " . $url);
    exit;
}

// Путь к конфигурации внутри protected
$config = require(__DIR__ . '/protected/config/web.php');

(new yii\web\Application($config))->run();
