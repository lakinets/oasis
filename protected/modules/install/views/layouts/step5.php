<?php
use yii\helpers\Html;
?>
<h1><?= Yii::t('install', 'Шаг 5: Завершено') ?></h1>
<p class="alert alert-success mt-3"><?= Yii::t('install', 'Установка успешно завершена.') ?></p>
<ul class="list-unstyled">
    <li>- <?= Yii::t('install', 'Удалите папку {folder}', ['folder' => '<code>/modules/install</code>']) ?></li>
    <li>- <?= Yii::t('install', 'Удалите файл {file}, если хотите переустановить позже', ['file' => '<code>/config/db.php</code>']) ?></li>
</ul>
<div class="mt-4">
    <?= Html::a(Yii::t('install', 'Перейти на сайт'), Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
</div>