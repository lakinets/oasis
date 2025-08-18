<?php
require_once 'vendor/autoload.php';
require_once 'vendor/yiisoft/yii2/Yii.php';
$config = require(__DIR__ . '/protected/config/web.php');
(new yii\web\Application($config));

use app\modules\backend\models\Users;

$login    = 'roottest';
$password = 'roottest';

$user = Users::findOne(['login' => $login]);
if (!$user) {
    echo "Пользователь не найден\n";
    exit;
}

echo "login: {$user->login}\n";
echo "auth_hash: {$user->auth_hash}\n";
echo "calculated MD5: " . md5($password) . "\n";
echo "validatePassword: " . ($user->validatePassword($password) ? 'true' : 'false') . "\n";