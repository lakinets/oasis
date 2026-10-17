<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<h1><?= Yii::t('install', 'Шаг 4: Создание администратора') ?></h1>
<?php $form = ActiveForm::begin(); ?>
    <div class="mt-3">
        <?= $form->field($model, 'login') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'email') ?>
    </div>
    <div class="form-group mt-4">
        <?= Html::submitButton(Yii::t('install', 'Завершить установку →'), ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>