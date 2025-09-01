<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $this->beginBody() ?>

<!-- Горизонтальное меню -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand">Кабинет</span>
    <div class="navbar-nav">
      <a class="nav-link <?= Yii::$app->controller->id === 'characters' ? 'active' : '' ?>"
         href="<?= Url::to(['/cabinet/characters']) ?>">Персонажи</a>
      <a class="nav-link <?= Yii::$app->controller->id === 'shop' ? 'active' : '' ?>"
         href="<?= Url::to(['/cabinet/shop']) ?>">Магазин</a>
      <a class="nav-link <?= Yii::$app->controller->id === 'deposit' ? 'active' : '' ?>"
         href="<?= Url::to(['/cabinet/deposit']) ?>">Пополнить</a>
      <a class="nav-link <?= Yii::$app->controller->id === 'security' ? 'active' : '' ?>"
         href="<?= Url::to(['/cabinet/security']) ?>">Безопасность</a>
    </div>
  </div>
</nav>

<div class="container">
    <?= $content ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>