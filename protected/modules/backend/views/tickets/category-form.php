<?php
/**
 * @var yii\web\View $this
 * @var app\modules\backend\models\TicketsCategories $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$isUpdate = (bool)$model->id;
$title_   = Yii::t('backend', 'Тикеты - категории');

$this->title = $isUpdate
    ? Yii::t('backend', 'Редактирование')
    : Yii::t('backend', 'Добавление категории');

$this->params['breadcrumbs'] = [
    ['label' => $title_, 'url' => ['categories']],
    $this->title,
];
?>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
    <div class="alert alert-<?= $type ?>"><?= $message ?></div>
<?php endforeach; ?>

<?php $form = ActiveForm::begin([
    'id'      => 'tickets-category-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-offset-3 col-lg-9\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
]); ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => $model->getAttributeLabel('title')]) ?>

<?= $form->field($model, 'sort')->textInput(['type' => 'number', 'placeholder' => $model->getAttributeLabel('sort')]) ?>

<?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
        <?= Html::submitButton(
            $isUpdate ? Yii::t('backend', 'Сохранить') : Yii::t('backend', 'Создать'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(Yii::t('backend', 'Отмена'), ['categories'], ['class' => 'btn btn-default']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>