<?php
use yii\helpers\Html;
use yii\captcha\Captcha;
use yii\widgets\ActiveForm;
use app\components\AppConfig;

$this->title = Yii::t('backend', 'Вход на сайт');
?>

<div class="login-box">
    <div class="login-logo">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"><?= Yii::t('backend', 'Введите свои данные') ?></p>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'login')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>

            <?php if (AppConfig::captchaEnabled()): ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                    'captchaAction' => 'login/captcha',
                    'options' => ['class' => 'form-control', 'placeholder' => Yii::t('backend', 'Код с картинки')],
                    'template' => '<div class="row"><div class="col-4">{image}</div><div class="col-8">{input}</div></div>',
                ]) ?>
            <?php endif; ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('backend', 'Войти'), ['class' => 'btn btn-primary btn-block']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

