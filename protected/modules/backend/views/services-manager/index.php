<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $services app\models\Services[] */

$this->title = Yii::t('backend', 'Управление сервисами');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.services-grid { margin-top: 20px; }
.service-item {
    border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px;
    background: #f9f9f9;
}
.service-item:hover { background: #f5f5f5; }
.service-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;
}
.service-name { font-weight: bold; font-size: 16px; }
.service-id { color: #999; font-size: 12px; }
.cost-input { width: 100px; text-align: center; }
.status-toggle { margin-left: 10px; }
.flash-messages { margin-bottom: 20px; }
");
?>

<div class="services-manager-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success flash-messages"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger flash-messages"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title"><?= Yii::t('backend', 'Список сервисов') ?></h3></div>
        <div class="panel-body">
            <div class="services-grid">
                <?php foreach ($services as $service): ?>
                    <?php
                    $safeName    = Html::encode($service->name);
                    $confirmText = $service->status
                        ? Yii::t('backend', "Выключить сервис '{service}'?", ['service' => $safeName])
                        : Yii::t('backend', "Включить сервис '{service}'?", ['service' => $safeName]);
                    ?>
                    <div class="service-item">
                        <div class="service-header">
                            <div>
                                <div class="service-name"><?= $safeName ?></div>
                                <div class="service-id">ID: <?= $service->id ?></div>
                            </div>
                            <div>
                                <!-- Форма обновления стоимости -->
                                <form method="get" action="<?= Url::to(['update']) ?>" class="form-inline" style="display: inline-block;">
                                    <input type="hidden" name="id" value="<?= $service->id ?>">
                                    <div class="input-group">
                                        <input type="number" name="cost" class="form-control cost-input"
                                               value="<?= $service->cost ?>"
                                               step="0.01" min="0" max="999999.99">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary btn-sm"><?= Yii::t('backend', 'Обновить') ?></button>
                                        </span>
                                    </div>
                                </form>

                                <!-- Переключатель статуса -->
                                <a href="<?= Url::to(['toggle', 'id' => $service->id]) ?>"
                                   class="btn btn-sm status-toggle <?= $service->status ? 'btn-success' : 'btn-danger' ?>"
                                   onclick="return confirm('<?= $confirmText ?>')">
                                    <i class="glyphicon glyphicon-<?= $service->status ? 'ok' : 'remove' ?>"></i>
                                    <?= $service->status ? Yii::t('backend', 'Включен') : Yii::t('backend', 'Выключен') ?>
                                </a>
                            </div>
                        </div>

                        <div class="service-info">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><?= Yii::t('backend', 'Текущая стоимость:') ?></strong>
                                    <span class="text-primary">
                                        <?= Yii::$app->formatter->asDecimal($service->cost) ?>
                                        Web Aden
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <strong><?= Yii::t('backend', 'Статус:') ?></strong>
                                    <span class="label label-<?= $service->status ? 'success' : 'danger' ?>">
                                        <?= $service->status ? Yii::t('backend', 'Включен') : Yii::t('backend', 'Выключен') ?>
                                    </span>
                                </div>
                                <div class="col-md-4"><strong><?= Yii::t('backend', 'ID сервиса:') ?></strong> <?= $service->id ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($services)): ?>
                    <div class="alert alert-info">
                        <i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('backend', 'Нет доступных сервисов') ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="well">
        <h4><i class="glyphicon glyphicon-info-sign"></i><?= Yii::t('backend', ' Инструкция по использованию:') ?></h4>
        <ul>
            <li><strong><?= Yii::t('backend', 'Изменение стоимости:') ?></strong><?= Yii::t('backend', ' введите новую сумму в поле и нажмите кнопку обновления') ?></li>
            <li><strong><?= Yii::t('backend', 'Включение/выключение:') ?></strong><?= Yii::t('backend', ' кликните на кнопку статуса для переключения') ?></li>
            <li><?= Yii::t('backend', 'Все изменения применяются мгновенно') ?></li>
            <li><?= Yii::t('backend', 'Только администраторы имеют доступ к этой странице') ?></li>
        </ul>
    </div>
</div>