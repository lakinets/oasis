<ul class="nav-mini">
    <li><a href="/cabinet/tickets"><?= Yii::t('views', 'Поддержка') ?></a></li>
    <li><a href="/cabinet/characters"><?= Yii::t('views', 'Персонажи') ?></a></li>
    <li><a href="/cabinet/shop"><?= Yii::t('views', 'Магазин') ?></a></li>
    <li><a href="/cabinet/bonuses"><?= Yii::t('views', 'Бонусы') ?></a></li>
    <li><a href="/cabinet/security"><?= Yii::t('views', 'Безопасность') ?></a></li>
    <li><a href="/cabinet/messages"><?= Yii::t('views', 'Сообщения') ?></a></li>
    
    <?php if (!\app\models\Gs::isTopupMode()): ?>
        <li><a href="/cabinet/deposit"><?= Yii::t('views', 'Пополнение') ?></a></li>
    <?php endif; ?>
    
    <li><a href="/cabinet/transaction-history"><?= Yii::t('views', 'История транзакций') ?></a></li>
    <li><a href="/cabinet/auth-history"><?= Yii::t('views', 'История входов') ?></a></li>
    <li><a href="/cabinet/referals"><?= Yii::t('views', 'Рефералы') ?></a></li>
    
    <?php if (!\app\models\Gs::isTopupMode()): ?>
        <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
    <?php endif; ?>
</ul>
<?php
use yii\widgets\ListView;

$this->title = Yii::t('views', 'Бонусы');
?>
<h1><?= Yii::t('views', 'Бонусы') ?></h1>

<div class="mb-3">
    <a href="/cabinet/bonuses/bonus-code" class="btn btn-warning"><?= Yii::t('views', 'Активация бонусов') ?></a>
</div>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_user_bonus',
    'summary' => '',
]) ?>