<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Безопасность';
?>
<h1>Безопасность</h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'protected_ip')->textarea(['rows' => 4, 'placeholder' => "192.168.1.1\n192.168.1.2"])->label('Разрешённые IP') ?>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>