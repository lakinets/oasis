<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Создать тикет';
?>
<h1>Создать тикет</h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'category_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(\app\modules\cabinet\models\TicketsCategories::find()->where(['status' => 1])->all(), 'id', 'title')
) ?>

<?= $form->field($model, 'priority')->dropDownList([
    0 => 'Низкий',
    1 => 'Средний',
    2 => 'Высокий',
]) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

<div class="form-group">
    <?= Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>