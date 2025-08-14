<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\LoginForm */

$this->title = 'Вход в админ-панель';
?>
<div class="login-box">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'login')->textInput(['autofocus' => true, 'placeholder' => 'Логин']) ?>
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль']) ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>