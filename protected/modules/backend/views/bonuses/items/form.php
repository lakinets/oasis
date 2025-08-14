<?php
/**
 * @var yii\web\View $this
 * @var app\modules\backend\models\Bonuses $bonus
 * @var app\modules\backend\models\BonusesItems $model
 * @var yii\widgets\ActiveForm $form
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('@web/themes/backend/assets/js/typeahead.bundle.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('@web/themes/backend/assets/js/search-items.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$title_ = Yii::t('backend', 'Бонусы');
$this->title = $title_;
$this->params['breadcrumbs'] = [
    ['label' => $title_, 'url' => ['/backend/bonuses/index']],
    ['label' => $bonus->title, 'url' => ['/backend/bonuses/items', 'bonus_id' => $bonus->id]],
    Yii::t('backend', Yii::$app->request->get('id') ? 'Редактирование' : 'Добавление'),
];

$this->registerJs("var urlItemInfo = '" . Url::to(['/backend/default/get-item-info']) . "';");
\app\widgets\FlashMessages\FlashMessages::widget();
?>

<style>
    .img-block { position: relative; }
    .img-block .img {
        position: absolute;
        top: 0;
        right: -20px;
        width: 32px;
        height: 32px;
    }
</style>

<p class="help-block text-danger">
    <b>*</b> <?= Yii::t('backend', 'Чтобы добавить предмет достаточно начать набирать его название или ввести его ID') ?>
</p>

<?php $form = ActiveForm::begin([
    'id' => 'bonuses-items-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-9\">{input}\n{error}</div>",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
]); ?>

<?= $form->errorSummary($model) ?>

<?= Html::hiddenInput('old_item_id', $model->item_id) ?>

<div class="form-group">
    <?= $form->label($model, 'item_name', ['class' => 'col-lg-3 control-label']) ?>
    <div class="col-lg-9 img-block">
        <div class="img">
            <?php if (!$model->isNewRecord && $model->itemInfo): ?>
                <?= $model->itemInfo->getIcon() ?>
            <?php endif ?>
        </div>
        <?= $form->field($model, 'item_name', ['template' => '{input}'])
                 ->textInput(['class' => 'form-control js-item-name']) ?>
        <p class="help-block"><?= Yii::t('backend', 'Название пишется по Русски (База синхронизирована с РУ офом)') ?></p>
    </div>
</div>

<?= $form->field($model, 'item_id')->textInput(['class' => 'form-control js-item-id', 'placeholder' => '57']) ?>
<p class="help-block"><?= Yii::t('backend', '57 - Адена, 4037 - Coin of luck') ?></p>

<?= $form->field($model, 'count')->textInput(['type' => 'number', 'min' => 1]) ?>

<?= $form->field($model, 'enchant')->textInput(['type' => 'number', 'min' => 0]) ?>

<?= $form->field($model, 'status')->dropDownList(
    [0 => Yii::t('backend', 'выкл'), 1 => Yii::t('backend', 'вкл')],
    ['class' => 'form-control']
) ?>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
        <?= Html::submitButton(
            $model->isNewRecord ? Yii::t('backend', 'Создать') : Yii::t('backend', 'Сохранить'),
            ['class' => 'btn btn-primary']
        ) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
// если используете kartik\datetime\DateTimePicker
// $this->registerJs("
//     $('#bonusesitems-date_end').datetimepicker({
//         format: 'YYYY-MM-DD HH:mm:ss',
//         useSeconds: true
//     });
// ");
?>