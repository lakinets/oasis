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
use yii\widgets\ListView;

$this->title = 'Бонусы';
?>
<h1>Бонусы</h1>

<div class="mb-3">
    <a href="/cabinet/bonuses/bonus-code" class="btn btn-warning">Активация бонусов</a>
</div>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_user_bonus',
    'summary' => '',
]) ?>