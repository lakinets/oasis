<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopCategories */
/* @var $gs app\modules\backend\models\Gs */

$this->title = $model->isNewRecord ? 'Добавить категорию' : 'Редактировать';
$this->params['breadcrumbs'][] = ['label' => $gs->name, 'url' => ['shop', 'gs_id' => $gs->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-category-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>