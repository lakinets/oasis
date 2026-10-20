<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopCategories */
/* @var $gs    app\modules\backend\models\Gs */

$this->title = $model->isNewRecord ? Yii::t('backend', 'Добавить категорию') : Yii::t('backend', 'Редактировать категорию');
?>
<div class="container shop-category-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'name_en')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'link')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Активна')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('backend', 'Отмена'), ['/backend/game-servers/shop', 'gs_id' => $gs->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>