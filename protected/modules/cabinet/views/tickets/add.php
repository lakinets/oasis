<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\cabinet\models\TicketsCategories;

/* @var $this yii\web\View */
/* @var $model app\modules\cabinet\models\TicketsForm */
/* @var $servers array */

$form = ActiveForm::begin(['id' => 'ticket-form']);

// категории из таблицы tickets_categories
echo $form->field($model, 'category_id')->dropDownList(
    ArrayHelper::map(
        TicketsCategories::find()->orderBy(['title' => SORT_ASC])->all(),
        'id',
        'title'
    ),
    ['prompt' => 'Выберите категорию']
);

echo $form->field($model, 'priority')->dropDownList(
    \app\modules\cabinet\models\Tickets::getPriorityList()
);

echo $form->field($model, 'title')->textInput([
    'maxlength' => true,
    'placeholder' => 'Кратко опишите суть'
]);

echo $form->field($model, 'char_name')->textInput([
    'maxlength' => true,
    'placeholder' => 'Ваш игровой ник'
]);

echo $form->field($model, 'date_incident')->input('date');

// сервера из таблицы gs
$servers = ArrayHelper::map(
    \app\modules\backend\models\Gs::find()->all(),
    'id',
    'name'
);
echo $form->field($model, 'gs_id')->dropDownList($servers, ['prompt' => 'Выберите сервер']);

echo $form->field($model, 'text')->textarea([
    'rows' => 6,
    'placeholder' => 'Подробно опишите проблему'
]);

echo Html::submitButton('Отправить', ['class' => 'btn btn-primary']);

ActiveForm::end();
