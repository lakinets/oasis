<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopItemsPacks */

$this->title = $model->isNewRecord ? 'Добавить набор' : 'Редактировать';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pack-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'price')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>