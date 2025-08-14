<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\BonusActivateForm $model */

$this->title = 'Активация бонуса';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonuses-activate">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        <div class="form-group">
            <?= Html::submitButton('Активировать', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
