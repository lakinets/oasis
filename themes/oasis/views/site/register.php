<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SignupForm */

$this->title = 'Регистрация';
?>

<section class="vs-auth-wrapper space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="auth-form">
                    <h2 class="form-title text-center mb-4"><?= Html::encode($this->title) ?></h2>

                    <?php $form = ActiveForm::begin([
                        'id' => 'register-form',
                        'options' => ['class' => 'vs-form'],
                        'fieldConfig' => [
                            'template' => '<div class="form-group mb-4">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'vs-label'],
                            'inputOptions' => ['class' => 'form-control vs-input'],
                            'errorOptions' => ['class' => 'invalid-feedback d-block'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username') ?>
                    <?= $form->field($model, 'email') ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <div class="form-group text-center">
                        <?= Html::submitButton('Зарегистрироваться', ['class' => 'vs-btn vs-btn-primary w-100']) ?>
                    </div>

                    <div class="text-center mt-3">
                        Уже есть аккаунт? <a href="<?= \yii\helpers\Url::to(['/login']) ?>">Войти</a>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>