<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход в админку';
$this->context->layout = 'login'; // необязательно, если задано в контроллере
?>

<div class="site-login" style="max-width:400px;margin:50px auto;">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Введите логин и пароль:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>

    <?= $form->field($model, 'login')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
