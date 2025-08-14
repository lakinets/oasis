<?php
require_once 'vendor/autoload.php';
require_once 'vendor/yiisoft/yii2/Yii.php';
$config = require(__DIR__ . '/protected/config/web.php');
(new yii\web\Application($config));

$hash = '$2y$13$SbN4u9ymuH9vQFovJHdKmeAP5MF4AH9sKTxTBmT3t6RoTC1TJShje';

$passwords = ['roottest', 'root', 'admin', 'password', '123456', 'testtest'];

foreach ($passwords as $p) {
    if (password_verify($p, $hash)) {
        echo "Пароль найден: $p\n";
        exit;
    }
}

echo "Пароль не найден среди проверенных\n";