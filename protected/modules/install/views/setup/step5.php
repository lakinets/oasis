<?php
use yii\helpers\Html;

/* @var bool $removed */
/* @var string|null $error */
?>
<h1>Шаг 5: Завершение</h1>

<?php if ($removed): ?>
    <p class="alert alert-success">
        Установка завершена. Папка <code>/protected/modules/install</code> удалена автоматически.
    </p>
<?php elseif ($error): ?>
    <p class="alert alert-warning">
        Установка завершена, но не удалось удалить папку <code>/protected/modules/install</code>.<br>
        Ошибка: <?= Html::encode($error) ?><br>
        Пожалуйста, удалите папку вручную.
    </p>
<?php else: ?>
    <p class="alert alert-warning">
        Установка завершена, но папка <code>/protected/modules/install</code> всё ещё существует.<br>
        Удалите её вручную для безопасности.
    </p>
<?php endif; ?>

<ul>
    <li>Если нужны изображения на сайте всех предметов — смотрите папку <code>Все предметы</code>.</li>
    <li>Удачного вам открытия!</li>
</ul>

<?= Html::a('На сайт', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
