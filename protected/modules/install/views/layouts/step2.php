<?php
/** @var $model app\modules\install\models\DbForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<h1>Step 2: Database</h1>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'host') ?>
    <?= $form->field($model, 'port') ?>
    <?= $form->field($model, 'db') ?>
    <?= $form->field($model, 'user') ?>
    <?= $form->field($model, 'pass')->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Next →', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>