<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $content string */
?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <style>
        nav { background:#eee; padding:10px; }
        nav a { margin-right:15px; text-decoration:none; color:#036; }
    </style>
</head>
<body>
<nav>
    <?= Html::a('Главная', ['/']) ?>
    <?= Html::a('Статистика', ['/stats']) ?>
    <?= Html::a('Галерея', ['/gallery']) ?>
    <?= Html::a('Вход', ['/login']) ?>
	    <?= Html::a('Регистрация', ['/register']) ?>
    <?= Html::a('Кабинет', ['/cabinet']) ?>
</nav>

<main>
    <?= $content ?>
</main>

<footer>
    <p>© 2025</p>
</footer>
</body>
</html>