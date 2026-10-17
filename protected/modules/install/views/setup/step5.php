<?php
use yii\helpers\Html;

/* @var bool $removed */
/* @var string|null $error */
?>
<h1><?= Yii::t('install', 'Шаг 5: Завершение') ?></h1>

<?php if ($removed): ?>
    <p class="alert alert-success mt-3">
        <?= Yii::t('install', 'Установка завершена. Папка {folder} удалена автоматически.', ['folder' => '<code>/protected/modules/install</code>']) ?>
    </p>
<?php elseif ($error): ?>
    <p class="alert alert-warning mt-3">
        <?= Yii::t('install', 'Установка завершена, но не удалось удалить папку {folder}.', ['folder' => '<code>/protected/modules/install</code>']) ?><br>
        <?= Yii::t('install', 'Ошибка:') ?> <?= Html::encode($error) ?><br>
        <?= Yii::t('install', 'Пожалуйста, удалите папку вручную.') ?>
    </p>
<?php else: ?>
    <p class="alert alert-warning mt-3">
        <?= Yii::t('install', 'Установка завершена, но папка {folder} всё ещё существует.', ['folder' => '<code>/protected/modules/install</code>']) ?><br>
        <?= Yii::t('install', 'Удалите её вручную для безопасности.') ?>
    </p>
<?php endif; ?>

<ul class="mt-3">
    <li><?= Yii::t('install', 'Если нужны изображения на сайте всех предметов — смотрите папку {folder}.', ['folder' => '<code>Все предметы</code>']) ?></li>
    <li><?= Yii::t('install', 'Удачного вам открытия!') ?></li>
</ul>

<div class="mt-4">
    <?= Html::a(Yii::t('install', 'На сайт'), Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
</div>