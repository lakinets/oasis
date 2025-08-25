<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\backend\models\AllItems;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\ShopItems */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $pack app\modules\backend\models\ShopItemsPacks */
?>

<div class="container shop-item-form">
    <h1><?= Html::encode($model->isNewRecord ? 'Добавить предмет' : 'Редактировать предмет') ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_id')->textInput(['type' => 'number', 'min' => 1])->label('ID предмета') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Если пусто, подставится автоматически из базы данных']) ?>

    <?= $form->field($model, 'count')->input('number', ['min' => 1])->label('Кол-во') ?>

    <?= $form->field($model, 'enchant')->input('number', ['min' => 0])->label('Заточка') ?>

    <?= $form->field($model, 'cost')->input('number', ['step' => 0.01, 'min' => 0])->label('Цена (Web Adena)') ?>

    <?= $form->field($model, 'discount')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 100])->label('Скидка (%)') ?>

    <?= $form->field($model, 'currency_type')->dropDownList([
        'donat' => 'Web Adena',
        'vote'  => 'Голоса',
    ])->label('Валюта покупки') ?>

    <?= $form->field($model, 'status')->checkbox(['label' => 'Активен']) ?>

    <?= $form->field($model, 'sort')->input('number', ['min' => 0])->label('Сортировка') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            'Отмена',
            ['/backend/game-servers/shop-pack', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
            ['class' => 'btn btn-secondary ml-2']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>