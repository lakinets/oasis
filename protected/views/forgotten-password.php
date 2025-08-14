<?php
/**
 * @var ForgottenPasswordForm $model
 * @var ActiveForm $form
 */

$title_ = Yii::t('main', 'Восстановление пароля от аккаунта');
$this->pageTitle = $title_;
?>

<div class="inner">
    <h2><?php echo e($title_) ?></h2>

    <?php $form = $this->beginWidget('ActiveForm', array(
        'id' => 'forgotten-password-form',
        'htmlOptions' => array(
            'class' => 'form-horizontal',
        ),
    )) ?>

        <?php echo $form->errorSummary($model) ?>

        <?php $this->widget('app.widgets.FlashMessages.FlashMessages') ?>

        <?php if(count($model->gs_list) > 1) { ?>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'gs_id') ?>
                <div class="field">
                    <?php echo $form->dropDownList($model, 'gs_id', CHtml::listData($model->gs_list, 'id', 'name'), array('class' => 'form-control')) ?>
                </div>
            </div>
        <?php } ?>
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'login') ?>
            <div class="field">
                <?php echo $form->textField($model, 'login', array('placeholder' => $model->getAttributeLabel('login'), 'class' => 'form-control')) ?>
                <p class="help-block">
                    <?php echo Yii::t('main', 'Длина должна быть от :min до :max символов', array(':min' => Users::LOGIN_MIN_LENGTH, ':max' => Users::LOGIN_MAX_LENGTH)) ?><br>
                </p>
            </div>
        </div>
        <div class="form-group clearfix">
            <?php echo $form->labelEx($model, 'email') ?>
            <div class="field">
                <?php echo $form->textField($model, 'email', array('placeholder' => $model->getAttributeLabel('email'), 'class' => 'form-control')) ?>
            </div>
        </div>
        <?php if(CCaptcha::checkRequirements() && config('forgotten_password.captcha.allow')) { ?>
            <div class="form-group clearfix">
                <?php echo $form->labelEx($model, 'verifyCode') ?>
                <div class="field captcha">
                    <?php echo $form->textField($model, 'verifyCode', array('placeholder' => $model->getAttributeLabel('verifyCode'), 'class' => 'form-control')) ?>
                    <div class="captcha-image">
                        <?php $this->widget('CCaptcha', array(
                            'id' => 'forgotten-password-form-captcha'
                        )) ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="button-group center">
            <button type="submit" class="button">
                <span><?php echo Yii::t('main', 'Восстановить') ?></span>
            </button>
        </div>

    <?php $this->endWidget() ?>
</div>