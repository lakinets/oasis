<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
use app\helpers\Config;

/** @var yii\web\View $this */
/** @var app\modules\auth\models\LoginForm $model */

$this->title = 'Авторизация';
$captchaOn = Config::isOn('login.captcha.allow');
?>
<div class="auth-form layout-public login-form">
    <h2 class="form-title"><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'login')
            ->textInput(['autofocus' => true, 'class' => 'form-control'])
            ->label('Логин', ['class' => 'form-label']) ?>

        <?= $form->field($model, 'password')
            ->passwordInput(['class' => 'form-control'])
            ->label('Пароль', ['class' => 'form-label']) ?>

        <?php if ($captchaOn): ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                'captchaAction' => '/auth/default/captcha',
                'template' => '<div class="captcha-inline">{image}{input}</div>',
                'options' => ['class' => 'form-control captcha-input'],
            ])->label('Код с картинки', ['class' => 'form-label']) ?>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <div class="form-group text-center">
            <?= Html::a('Забыли пароль?', ['/auth/default/request-password-reset']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>