<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ShopItems $model */
/** @var app\modules\backend\models\Gs $gs */
/** @var app\modules\backend\models\ShopCategories $category */
/** @var app\modules\backend\models\ShopItemsPacks $pack */

$this->title = $model->isNewRecord
    ? Yii::t('backend', 'Добавление предмета')
    : Yii::t('backend', 'Редактирование предмета');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Сервера'), 'url' => ['/backend/game-servers/index']];
$this->params['breadcrumbs'][] = [
    'label' => $gs->name . ' - ' . Yii::t('backend', 'Магазин'),
    'url' => ['/backend/game-servers/shop', 'gs_id' => $gs->id]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('backend', 'Наборы для категории - :category_name', ['category_name' => $category->name]),
    'url' => ['/backend/game-servers/shop-category-packs', 'gs_id' => $gs->id, 'category_id' => $category->id]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('backend', 'Предметы в наборе - :pack_name', ['pack_name' => $pack->title]),
    'url' => ['/backend/game-servers/shop-category-pack-items', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id]
];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/typeahead.bundle.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('/js/search-items.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("var urlItemInfo = " . json_encode(Url::to(['/backend/default/get-item-info'])) . ";", \yii\web\View::POS_HEAD);
?>

<div class="item-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-danger">
        <small><?= Yii::t('backend', 'Чтобы добавить предмет достаточно начать набирать его название или ввести его ID') ?></small>
    </p>

    <?php $form = ActiveForm::begin([
        'id' => 'item-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-9\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]); ?>

    <?= Html::hiddenInput('old_item_id', $model->item_id) ?>

    <?= $form->field($model, 'item_name')->textInput(['class' => 'form-control js-item-name']) ?>
    <?= $form->field($model, 'item_id')->textInput(['class' => 'form-control js-item-id']) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'cost')->input('number') ?>
    <?= $form->field($model, 'discount')->input('number', ['placeholder' => Yii::t('backend', 'Введите число без %')]) ?>
    <?= $form->field($model, 'count')->input('number') ?>
    <?= $form->field($model, 'enchant')->input('number') ?>
    <?= $form->field($model, 'sort')->input('number') ?>
    <?= $form->field($model, 'status)->dropDownList($model->getStatusList()) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Создать') : Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('backend', 'Назад'), ['/backend/game-servers/shop-category-pack-items', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>