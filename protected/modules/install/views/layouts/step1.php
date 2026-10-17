<?php
/** @var $model app\modules\install\models\PhpCheckForm */
use yii\helpers\Html;
?>
<div class="site-install-step1">
    <h1><?= Yii::t('install', 'Шаг 1: Проверка окружения') ?></h1>
    <ul class="list-unstyled mt-3">
        <li class="mb-2"><?= Yii::t('install', 'PHP ≥ 8.2') ?>: 
            <b class="text-<?= $model->checkPhp()?'success':'danger' ?>">
                <?= $model->checkPhp() ? Yii::t('install', 'УСПЕШНО') : Yii::t('install', 'ОШИБКА') ?>
            </b>
        </li>
        <li class="mb-2"><?= Yii::t('install', 'Расширение PDO MySQL') ?>: 
            <b class="text-<?= $model->checkPdo()?'success':'danger' ?>">
                <?= $model->checkPdo() ? Yii::t('install', 'УСПЕШНО') : Yii::t('install', 'ОШИБКА') ?>
            </b>
        </li>
        <li class="mb-2"><?= Yii::t('install', 'Расширение GD') ?>: 
            <b class="text-<?= $model->checkGd()?'success':'danger' ?>">
                <?= $model->checkGd() ? Yii::t('install', 'УСПЕШНО') : Yii::t('install', 'ОШИБКА') ?>
            </b>
        </li>
    </ul>
    <div class="mt-4">
        <?= Html::a(Yii::t('install', 'Далее →'), ['step2'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>