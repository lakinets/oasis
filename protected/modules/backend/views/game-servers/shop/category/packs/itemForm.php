<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ShopItemsPacks $model */
/** @var app\modules\backend\models\Gs $gs */
/** @var app\modules\backend\models\ShopCategories $category */

$this->title = $model->isNewRecord
    ? Yii::t('backend', 'Создание набора')
    : Yii::t('backend', 'Редактирование набора');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Сервера'), 'url' => ['/backend/game-servers/index']];
$this->params['breadcrumbs'][] = [
    'label' => $gs->name . ' - ' . Yii::t('backend', 'Магазин'),
    'url' => ['/backend/game-servers/shop', 'gs_id' => $gs->id]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('backend', 'Наборы для категории - :category_name', ['category_name' => $category->name]),
    'url' => ['/backend/game-servers/shop-category-packs', 'gs_id' => $gs->id, 'category_id' => $category->id]
];
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
    $('.js-del-image').on('click', function(e) {
        e.preventDefault();
        $.getJSON($(this).attr('href')).done(function(res) {
            if (res.status === 'success') {
                location.reload();
            }
        });
    });
JS;
$this->registerJs($js);
?>

<div class="pack-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'pack-form',
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-9\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
    <?= $form->field($model, 'sort')->input('number') ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    <?php if (!$model->isNewRecord && $model->imgIsExists()): ?>
        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-9">
                <?= Html::img($model->getImgUrl(), ['style' => 'max-width: 200px;']) ?><br>
                <?= Html::a(Yii::t('backend', 'Удалить картинку'), ['/backend/game-servers/shop-category-pack-del-image', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $model->id], ['class' => 'btn btn-danger btn-sm js-del-image']) ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'img')->fileInput() ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Создать') : Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('backend', 'Назад'), ['/backend/game-servers/shop-category-packs', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>