<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */
/* @var $form ActiveForm */

$this->title = 'Вход в аккаунт';
?>

<section class="vs-auth-wrapper space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="auth-form">
                    <h2 class="form-title text-center mb-4"><?= Html::encode($this->title) ?></h2>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'options' => ['class' => 'vs-form'],
                        'fieldConfig' => [
                            'template' => '<div class="form-group mb-4">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'vs-label'],
                            'inputOptions' => ['class' => 'form-control vs-input'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => '<div class="form-check mb-3">{input} {label}</div>',
                        'labelOptions' => ['class' => 'form-check-label'],
                        'inputOptions' => ['class' => 'form-check-input'],
                    ]) ?>

                    <div class="form-group text-center">
                        <?= Html::submitButton('Войти', ['class' => 'vs-btn vs-btn-primary w-100']) ?>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= \yii\helpers\Url::to(['/register']) ?>">Нет аккаунта? Зарегистрируйтесь</a><br>
                        <a href="<?= \yii\helpers\Url::to(['/request-password-reset']) ?>">Забыли пароль?</a>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>