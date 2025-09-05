<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\captcha\Captcha;

/** @var \app\modules\register\models\RegisterForm $model */
/** @var bool $prefixesEnabled */
/** @var bool $captchaEnabled */
/** @var bool $referralsEnabled */

$this->title = 'Регистрация';
?>
<div class="register-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(['id' => 'register-form']); ?>

    <?= $form->field($model, 'gs_id')->dropDownList(
        ArrayHelper::map($model->gs_list, 'id', 'name'),
        ['prompt' => 'Выберите сервер']
    ) ?>

    <?php if ($prefixesEnabled): ?>
        <div class="mb-3">
            <label class="form-label fw-bold">Префикс (назначен автоматически)</label>
            <div class="form-control-plaintext" style="padding-left:0;">
                <code><?= Html::encode($model->prefix) ?></code>
            </div>
            <!-- скрытое поле, чтобы передать значение обратно -->
            <?= Html::activeHiddenInput($model, 'prefix') ?>
            <div class="form-text">Префикс добавится к вашему логину автоматически.</div>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 're_password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php if ($referralsEnabled): ?>
        <?= $form->field($model, 'referer')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <?php if ($captchaEnabled): ?>
        <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
            'captchaAction' => 'register/default/captcha',
            'options' => ['class' => 'form-control', 'placeholder' => 'Код с картинки'],
            'template' => '<div class="row"><div class="col-sm-4">{image}</div><div class="col-sm-8">{input}</div></div>',
        ]) ?>
    <?php endif; ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>