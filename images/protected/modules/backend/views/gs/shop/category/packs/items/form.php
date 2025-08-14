<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopItems */

$this->title = $model->isNewRecord ? 'Добавить предмет' : 'Редактировать';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_id')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'amount')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'enchant')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>