<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Gs */

$this->title = $model->isNewRecord ? 'Добавить сервер' : 'Редактировать';
$this->params['breadcrumbs'][] = ['label' => 'Серверы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gs-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'port')->textInput(['type' => 'number']) ?>
    <?= $form->field($model, 'status')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>