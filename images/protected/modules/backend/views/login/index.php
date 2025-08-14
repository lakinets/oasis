<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Авторизация';
?>

<div class="login-box">
    <div class="login-logo">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Вход в админ-панель</p>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'login')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
