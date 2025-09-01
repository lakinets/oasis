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

$this->title = 'Оплата успешна';
?>
<h1>Оплата успешна</h1>

<p>Провайдер: <b><?= Html::encode($provider) ?></b></p>
<p>Заказ: <b><?= Html::encode($orderId) ?></b></p>

<p>
    Если баланс не обновился сразу, обновите страницу.
    Начисление выполняется по колбэку.
</p>
