<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\ShopCategories $model */
/** @var app\modules\backend\models\Gs $gs */

$this->title = $model->isNewRecord
    ? Yii::t('backend', 'Создание категории')
    : Yii::t('backend', 'Редактирование категории');

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Сервера'), 'url' => ['/backend/game-servers/index']];
$this->params['breadcrumbs'][] = ['label' => $gs->name . ' - ' . Yii::t('backend', 'Магазин'), 'url' => ['/backend/game-servers/shop', 'gs_id' => $gs->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="shop-category-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'shop-category-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-9\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Создать') : Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('backend', 'Назад'), ['/backend/game-servers/shop', 'gs_id' => $gs->id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>