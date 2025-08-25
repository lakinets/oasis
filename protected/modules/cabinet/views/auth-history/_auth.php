<?php
/** @var app\modules\cabinet\models\UsersAuthLogs $model */

// Настройка форматтера прямо в шаблоне
$formatter = Yii::$app->formatter;
$formatter->locale = 'ru-RU';                 // язык
$formatter->timeZone = 'Europe/Moscow';       // часовой пояс (поменяй при необходимости)
$formatter->defaultTimeZone = 'UTC';          // если в БД время в UTC
$formatter->datetimeFormat = 'php:d.m.Y H:i'; // формат даты и времени (24ч)
?>
<div class="card mb-2">
    <div class="card-body">
        <p><strong>IP:</strong> <?= $model->ip ?></p>
        <p><strong>Браузер:</strong> <?= $model->user_agent ?></p>
        <small class="text-muted">
            <?= $formatter->asDatetime($model->created_at) ?>
        </small>
    </div>
</div>

