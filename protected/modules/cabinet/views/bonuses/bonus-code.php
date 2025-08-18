<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Активировать бонус-код';
?>
<h1>Активировать бонус-код</h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'code')->textInput(['placeholder' => 'Введите код']) ?>
<?= Html::submitButton('Активировать', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>