<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model app\modules\install\models\DbForm */
?>
<h1><?= Yii::t('install', 'Шаг 2: Подключение к базе данных.') ?></h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'host') ?>
<?= $form->field($model, 'port') ?>
<?= $form->field($model, 'db') ?>
<?= $form->field($model, 'user') ?>
<?= $form->field($model, 'pass')->passwordInput() ?>
<div class="form-group mt-4">
    <?= Html::submitButton(Yii::t('install', 'Дальше →'), ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>