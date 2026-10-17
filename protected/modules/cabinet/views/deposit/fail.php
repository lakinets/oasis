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

$this->title = Yii::t('views', 'Оплата не выполнена');
?>
<h1><?= Yii::t('views', 'Оплата не выполнена') ?></h1>

<p><?= Yii::t('views', 'Провайдер: {provider}', ['provider' => Html::encode($provider)]) ?></p>
<p><?= Yii::t('views', 'Заказ: {order}', ['order' => Html::encode($orderId)]) ?></p>

<?php if (!empty($msg)): ?>
    <p><?= Yii::t('views', 'Причина: {reason}', ['reason' => Html::encode($msg)]) ?></p>
<?php endif; ?>
