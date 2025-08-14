<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\LoginServers */

$this->title = $model->isNewRecord ? 'Добавить сервер' : 'Редактировать';
?>
<div class="login-servers-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'port')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>