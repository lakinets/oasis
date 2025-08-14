<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\EditUserForm */

$this->title = 'Редактировать пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="users-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="users-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'role')->dropDownList($model->getRoleList()) ?>
        <?= $form->field($model, 'activated')->dropDownList($model->getActivatedStatusList()) ?>
        <?= $form->field($model, 'balance')->textInput(['type' => 'number', 'step' => '0.01']) ?>
        <?= $form->field($model, 'phone')->textInput(['maxlength' => 54]) ?>
        <?= $form->field($model, 'protected_ip')->textarea(['rows' => 6, 'placeholder' => "127.0.0.1\n192.168.1.100"]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>