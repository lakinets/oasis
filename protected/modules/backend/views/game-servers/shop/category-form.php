<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopCategories */
/* @var $gs    app\modules\backend\models\Gs */

$this->title = $model->isNewRecord ? 'Добавить категорию' : 'Редактировать категорию';
?>
<div class="container shop-category-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255])->label('Название') ?>
    <?= $form->field($model, 'link')->textInput(['maxlength' => 255])->label('Ссылка') ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number'])->label('Порядок сортировки') ?>
    <?= $form->field($model, 'status')->checkbox(['label' => 'Активна']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['/backend/game-servers/shop', 'gs_id' => $gs->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>