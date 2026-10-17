<?php
/** @var $model app\modules\install\models\DbForm */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<h1><?= Yii::t('install', 'Шаг 2: База данных') ?></h1>
<?php $form = ActiveForm::begin(); ?>
    <div class="mt-3">
        <?= $form->field($model, 'host') ?>
        <?= $form->field($model, 'port') ?>
        <?= $form->field($model, 'db') ?>
        <?= $form->field($model, 'user') ?>
        <?= $form->field($model, 'pass')->passwordInput() ?>
    </div>
    <div class="form-group mt-4">
        <?= Html::submitButton(Yii::t('install', 'Далее →'), ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>