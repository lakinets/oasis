<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Tickets */
/* @var $answerModel app\modules\backend\models\TicketsAnswers */
/* @var $categories array [id => title] */
/* @var $servers array [id => name] */

$this->title = Yii::t('backend', 'Создать тикет');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Тикеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tickets-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => Yii::t('backend','Выберите категорию')]) ?>

    <?= $form->field($model, 'gs_id')->dropDownList($servers, ['prompt' => Yii::t('backend','Выберите сервер')]) ?>

    <?= $form->field($model, 'priority')->dropDownList(
        \app\modules\backend\models\Tickets::getPriorityList(),
        ['prompt' => Yii::t('backend','Выберите приоритет')]
    ) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'char_name')->textInput(['maxlength' => 255]) ?>

    <?php if (empty($model->date_incident)) $model->date_incident = date('Y-m-d\TH:i'); ?>
    <?= $form->field($model, 'date_incident')->input('datetime-local') ?>

    <?= $form->field($answerModel, 'text')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Создать тикет'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>