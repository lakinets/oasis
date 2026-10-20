<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopItemsPacks */
/* @var $gs    app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */

$this->title = $model->isNewRecord ? Yii::t('backend', 'Создать пак') : Yii::t('backend', 'Редактировать пак');
?>
<div class="container shop-pack-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'title_en')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'description_en')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Активен')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('backend', 'Отмена'), ['shop-category', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>