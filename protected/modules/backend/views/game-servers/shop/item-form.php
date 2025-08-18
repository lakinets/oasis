<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Добавить предмет в пак';
?>

<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_id')->textInput(['type' => 'number', 'placeholder' => 'ID предмета']) ?>
    <?= $form->field($model, 'count')->textInput(['type' => 'number', 'value' => 1]) ?>
    <?= $form->field($model, 'enchant')->textInput(['type' => 'number', 'value' => 0]) ?>
    <?= $form->field($model, 'cost')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['shop-pack', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>