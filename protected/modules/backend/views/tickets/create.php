<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\backend\models\Tickets;

/* @var $this        yii\web\View */
/* @var $model       app\modules\backend\models\Tickets */
/* @var $answerModel app\modules\backend\models\TicketsAnswers */
/* @var $categories  array */
/* @var $servers     array */

$this->title = 'Создать тикет';
$this->params['breadcrumbs'][] = ['label' => 'Тикеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tickets-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '-- выберите --']) ?>
    <?= $form->field($model, 'gs_id')->dropDownList($servers, ['prompt' => '-- выберите --']) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'priority')->dropDownList(Tickets::getPrioritiesList()) ?>
    <?= $form->field($model, 'char_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($answerModel, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>