<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model app\modules\install\models\DbForm */
?>
<h1>Шаг 2: Подключение к базе данных.</h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'host') ?>
<?= $form->field($model, 'port') ?>
<?= $form->field($model, 'db') ?>
<?= $form->field($model, 'user') ?>
<?= $form->field($model, 'pass')->passwordInput() ?>
<div class="form-group">
    <?= Html::submitButton('Дальше →', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>