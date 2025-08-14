<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $info array */

$this->title = 'Админ-панель';
?>
<div class="backend-default-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3">
            <!-- Кнопка «Очистить кеш» -->
            <div class="well">
                <?= Html::a(
                    'Очистить кеш',
                    ['clear-cache'],
                    ['class' => 'btn btn-danger btn-block']
                ) ?>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Таблица «Информация о проекте» -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Информация о проекте</h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                        <?php foreach ($info as $k => $v): ?>
                            <tr>
                                <td width="200"><strong><?= Html::encode($k) ?></strong></td>
                                <td><?= Html::encode($v) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>