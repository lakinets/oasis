<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\auth\models\PasswordResetRequestForm */

$this->title = Yii::t('views', 'Сброс пароля');
?>
<div class="auth-form layout-public request-password-reset">
    <h2 class="form-title"><?= Html::encode($this->title) ?></h2>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

        <?= $form->field($model, 'email', [
            'inputOptions' => ['class' => 'form-control', 'autofocus' => true],
            'labelOptions' => ['class' => 'form-label'],
        ])->textInput()->label(Yii::t('views', 'E-mail')) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('views', 'Отправить'), ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <div class="text-center">
        <?= Html::a(Yii::t('views', 'Вернуться ко входу'), ['/auth/default/login']) ?>
    </div>
</div>