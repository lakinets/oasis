<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopItems */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $pack app\modules\backend\models\ShopItemsPacks */
?>

<div class="container shop-item-form">
    <h1><?= Html::encode($model->isNewRecord ? Yii::t('backend', 'Добавить предмет') : Yii::t('backend', 'Редактировать предмет')) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_id')->textInput(['type' => 'number', 'min' => 1]) ?>

    <?= $form->field($model, 'description')->textarea([
        'rows' => 3,
        'placeholder' => Yii::t('backend', 'Если пусто, подставится автоматически из базы данных'),
    ]) ?>

    <?= $form->field($model, 'description_en')->textarea([
        'rows' => 3,
        'placeholder' => Yii::t('backend', 'Если пусто, подставится автоматически из базы данных (EN)'),
    ]) ?>

    <?= $form->field($model, 'count')->input('number', ['min' => 1]) ?>
    <?= $form->field($model, 'enchant')->input('number', ['min' => 0]) ?>
    <?= $form->field($model, 'cost')->input('number', ['step' => 0.01, 'min' => 0]) ?>
    <?= $form->field($model, 'discount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 100]) ?>

    <?= $form->field($model, 'currency_type')->dropDownList([
        'donat' => 'Web Adena',
        'vote'  => Yii::t('backend', 'Голоса'),
    ]) ?>

    <?= $form->field($model, 'status')->checkbox(['label' => Yii::t('backend', 'Активен')]) ?>
    <?= $form->field($model, 'sort')->input('number', ['min' => 0]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            Yii::t('backend', 'Отмена'),
            ['/backend/game-servers/shop-pack', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
            ['class' => 'btn btn-secondary ml-2']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>