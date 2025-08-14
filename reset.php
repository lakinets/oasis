<?php
require_once 'vendor/autoload.php';
require_once 'vendor/yiisoft/yii2/Yii.php';
$config = require(__DIR__ . '/protected/config/web.php');
(new yii\web\Application($config));

$user = \app\modules\backend\models\Users::findOne(['login' => 'roottest']);
if ($user) {
    $user->setPassword('roottest'); // создаст новый хеш
    $user->activated = 1;
    $user->save(false); // без валидации
    echo "Пароль обновлён для: {$user->login}\n";
} else {
    echo "Пользователь не найден\n";
}