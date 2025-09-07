<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<h1>Step 4: Create administrator</h1>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'login') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'email') ?>
    <div class="form-group">
        <?= Html::submitButton('Finish install →', ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>