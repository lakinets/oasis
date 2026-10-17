<?php
/* @var $this yii\web\View */
/* @var $page string */
$this->title = Yii::t('backend', 'Просмотр страницы: {page}', [
    'page' => htmlspecialchars($page),
]);
?>

<div class="backend-site-view">
    <h1><?= htmlspecialchars($page) ?></h1>
    <p><?= Yii::t('backend', 'Это страница: ') ?><strong><?= htmlspecialchars($page) ?></strong></p>
</div>

