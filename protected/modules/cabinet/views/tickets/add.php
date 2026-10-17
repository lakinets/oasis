<ul class="nav-mini">
    <li><a href="/cabinet/tickets"><?= Yii::t('views', 'Поддержка') ?></a></li>
    <li><a href="/cabinet/characters"><?= Yii::t('views', 'Персонажи') ?></a></li>
    <li><a href="/cabinet/shop"><?= Yii::t('views', 'Магазин') ?></a></li>
    <li><a href="/cabinet/bonuses"><?= Yii::t('views', 'Бонусы') ?></a></li>
    <li><a href="/cabinet/security"><?= Yii::t('views', 'Безопасность') ?></a></li>
    <li><a href="/cabinet/messages"><?= Yii::t('views', 'Сообщения') ?></a></li>
    <li><a href="/cabinet/deposit"><?= Yii::t('views', 'Пополнение') ?></a></li>
    <li><a href="/cabinet/transaction-history"><?= Yii::t('views', 'История транзакций') ?></a></li>
    <li><a href="/cabinet/auth-history"><?= Yii::t('views', 'История входов') ?></a></li>
    <li><a href="/cabinet/referals"><?= Yii::t('views', 'Рефералы') ?></a></li>
    <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
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

// Категории из таблицы tickets_categories (с локализацией)
echo $form->field($model, 'category_id')->dropDownList(
    TicketsCategories::getDropdownList(),
    ['prompt' => Yii::t('views', 'Выберите категорию')]
);

// Приоритет
echo $form->field($model, 'priority')->dropDownList(
    \app\modules\cabinet\models\Tickets::getPriorityList()
);

// Заголовок тикета
echo $form->field($model, 'title')->textInput([
    'maxlength' => true,
    'placeholder' => Yii::t('views', 'Кратко опишите суть')
]);

// Имя вашего игрового ника
echo $form->field($model, 'char_name')->textInput([
    'maxlength' => true,
    'placeholder' => Yii::t('views', 'Ваш игровой ник')
]);

// Дата инцидента
echo $form->field($model, 'date_incident')->input('date');

// Сервера из таблицы gs
$servers = ArrayHelper::map(
    \app\modules\backend\models\Gs::find()->all(),
    'id',
    'name'
);
echo $form->field($model, 'gs_id')->dropDownList($servers, ['prompt' => Yii::t('views', 'Выберите сервер')]);

// Описание проблемы
echo $form->field($model, 'text')->textarea([
    'rows' => 6,
    'placeholder' => Yii::t('views', 'Подробно опишите проблему')
]);

echo Html::submitButton(Yii::t('views', 'Отправить'), ['class' => 'btn btn-primary']);

ActiveForm::end();