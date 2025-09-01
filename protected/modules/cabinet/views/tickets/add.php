<ul class="nav-mini">
    <li><a href="/cabinet/tickets">Поддержка</a></li>
    <li><a href="/cabinet/characters">Персонажи</a></li>
    <li><a href="/cabinet/shop">Магазин</a></li>
    <li><a href="/cabinet/bonuses">Бонусы</a></li>
    <li><a href="/cabinet/security">Безопасность</a></li>
    <li><a href="/cabinet/messages">Сообщения</a></li>
    <li><a href="/cabinet/deposit">Пополнение</a></li>
    <li><a href="/cabinet/transaction-history">История транзакций</a></li>
    <li><a href="/cabinet/auth-history">История входов</a></li>
    <li><a href="/cabinet/referals">Рефералы</a></li>
    <li><a href="/cabinet/services">Услуги</a></li>
</ul>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\cabinet\models\TicketsCategories;

/* @var $this yii\web\View */
/* @var $model app\modules\cabinet\models\TicketsForm */
/* @var $servers array */

$form = ActiveForm::begin(['id' => 'ticket-form']);

// Категории из таблицы tickets_categories
echo $form->field($model, 'category_id')->dropDownList(
    ArrayHelper::map(
        TicketsCategories::find()->orderBy(['title' => SORT_ASC])->all(),
        'id',
        'title'
    ),
    ['prompt' => 'Выберите категорию']
);

// Приоритет
echo $form->field($model, 'priority')->dropDownList(
    \app\modules\cabinet\models\Tickets::getPriorityList()
);

// Заголовок тикета
echo $form->field($model, 'title')->textInput([
    'maxlength' => true,
    'placeholder' => 'Кратко опишите суть'
]);

// Имя вашего игрового ника
echo $form->field($model, 'char_name')->textInput([
    'maxlength' => true,
    'placeholder' => 'Ваш игровой ник'
]);

// Дата инцидента
echo $form->field($model, 'date_incident')->input('date');

// Сервера из таблицы gs
$servers = ArrayHelper::map(
    \app\modules\backend\models\Gs::find()->all(),
    'id',
    'name'
);
echo $form->field($model, 'gs_id')->dropDownList($servers, ['prompt' => 'Выберите сервер']);

// Описание проблемы
echo $form->field($model, 'text')->textarea([
    'rows' => 6,
    'placeholder' => 'Подробно опишите проблему'
]);

echo Html::submitButton('Отправить', ['class' => 'btn btn-primary']);

ActiveForm::end();

