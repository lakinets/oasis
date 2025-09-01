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
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Активировать бонус-код';
?>
<h1>Активировать бонус-код</h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'code')->textInput(['placeholder' => 'Введите код']) ?>
<?= Html::submitButton('Активировать', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
