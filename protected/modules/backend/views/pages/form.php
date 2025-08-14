<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\backend\models\Pages; // <- импорт модели Pages

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'page')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <!-- Поле в БД называется text, поэтому используем text -->
    <?= $form->field($model, 'text')->textarea(['rows' => 12]) ?>

    <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        Pages::STATUS_ON  => 'Активна',
        Pages::STATUS_OFF => 'Не активна',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class'=>'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
