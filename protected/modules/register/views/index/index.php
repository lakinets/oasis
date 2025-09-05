<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var \app\modules\register\models\RegisterForm $model */
/* @var bool $prefixesEnabled */
/* @var bool $captchaEnabled */
/* @var bool $referralsEnabled */

$this->title = 'Регистрация';
?>
<div class="register-form">
    <h1 class="orion-table-header"><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>

    <?= $form->field($model, 'gs_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($model->gs_list, 'id', 'name'),
        ['prompt' => 'Выберите сервер', 'class' => 'form-control btn-orion']
    ) ?>

    <?php if ($prefixesEnabled): ?>
        <div class="mb-3">
            <label class="form-label fw-bold">Префикс (назначен автоматически)</label>
            <div class="form-control-plaintext" style="padding-left: 0;">
                <code><?= Html::encode($model->prefix) ?></code>
            </div>
            <?= Html::activeHiddenInput($model, 'prefix') ?>
            <small class="form-text">Префикс добавится к вашему логину автоматически.</small>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true, 'class' => 'form-control btn-orion']) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'class' => 'form-control btn-orion']) ?>

    <?= $form->field($model, 're_password')->passwordInput(['maxlength' => true, 'class' => 'form-control btn-orion']) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class' => 'form-control btn-orion']) ?>

    <?php if ($referralsEnabled): ?>
        <?= $form->field($model, 'referer')->textInput(['maxlength' => true, 'class' => 'form-control btn-orion']) ?>
    <?php endif; ?>

    <?php if ($captchaEnabled): ?>
        <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::class, [
            'captchaAction' => 'default/captcha',
            'options'       => ['class' => 'form-control btn-orion'],
            'template'      => '<div class="row"><div class="col-sm-4">{image}</div><div class="col-sm-8">{input}</div></div>',
        ]) ?>
    <?php endif; ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-danger btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>