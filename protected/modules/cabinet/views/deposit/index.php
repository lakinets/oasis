<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Пополнение баланса';
?>
<h1>Пополнение баланса</h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'sum')->textInput(['type' => 'number', 'min' => 1]) ?>
<?= Html::submitButton('Пополнить', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>