<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\backend\models\Bonuses; // Добавил подключение модели

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Bonuses */

$this->title = $model->isNewRecord ? 'Создать бонус' : 'Редактировать бонус';
$this->params['breadcrumbs'][] = ['label' => 'Бонусы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonuses-form">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'date_end')->textInput(['placeholder' => 'YYYY-MM-DD HH:ii:ss']) ?>
    <?= $form->field($model, 'status')->dropDownList(Bonuses::getStatusList()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
