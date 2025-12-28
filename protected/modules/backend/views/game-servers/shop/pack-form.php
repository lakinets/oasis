<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopItemsPacks */
/* @var $gs    app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */

$this->title = $model->isNewRecord ? 'Создать пак' : 'Редактировать пак';
?>
<div class="container shop-pack-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255])->label('Название') ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3])->label('Описание') ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number'])->label('Порядок сортировки') ?>
    <?= $form->field($model, 'status')->checkbox(['label' => 'Активен']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['shop-category', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>