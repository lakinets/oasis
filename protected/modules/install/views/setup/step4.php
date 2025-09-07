<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/** @var $model app\modules\install\models\AdminForm */
?>
<h1>Шаг 4: Создание админстратора</h1>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'login') ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<?= $form->field($model, 'email') ?>
<div class="form-group">
    <?= Html::submitButton('Завершить установку', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>