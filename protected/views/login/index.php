<?php
/** @var yii\web\View $this */
/** @var app\models\forms\LoginForm $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Авторизация';
?>
<div class="container" style="max-width: 420px; margin: 40px auto;">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type ?>"><?= $message ?></div>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'login')->textInput(['autocomplete' => 'username']) ?>
    <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'current-password']) ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary w-100']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>