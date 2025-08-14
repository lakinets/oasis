<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = Html::encode(
    ArrayHelper::getValue($page, 'seo_title', ArrayHelper::getValue($page, 'title', 'Главная страница'))
);
?>
<div class="jumbotron text-center">
    <h1><?= Html::encode(ArrayHelper::getValue($page, 'title', 'Главная страница')) ?></h1>
    <p class="lead"><?= Html::encode(ArrayHelper::getValue($page, 'text', 'Добро пожаловать на наш сайт!')) ?></p>
</div>
