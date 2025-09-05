<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\auth\models\ResetPasswordForm */

$this->title = 'Новый пароль';
?>
<div class="auth-form layout-public reset-password">
    <h2 class="form-title"><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password', [
            'inputOptions' => ['class' => 'form-control'],
            'labelOptions' => ['class' => 'form-label'],
        ])->passwordInput()->label('Новый пароль') ?>

        <?= $form->field($model, 'passwordRepeat', [
            'inputOptions' => ['class' => 'form-control'],
            'labelOptions' => ['class' => 'form-label'],
        ])->passwordInput()->label('Повтор пароля') ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>