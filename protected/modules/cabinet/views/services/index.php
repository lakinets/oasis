<?php
use yii\helpers\Html;
use yii\helpers\Url;   // ← эту строку добавили
use app\models\Services;

$this->title = Yii::t('views', 'Услуги');
?>

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
    <li><a href="/cabinet/services" class="active"><?= Yii::t('views', 'Услуги') ?></a></li>
</ul>

<h1><?= Yii::t('views', 'Игровые услуги') ?></h1>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success" style="padding: 15px; background: rgba(0,255,0,0.1); border: 1px solid #00ff00; margin-bottom: 20px; border-radius: 4px;">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger" style="padding: 15px; background: rgba(255,0,0,0.1); border: 1px solid #ff0000; margin-bottom: 20px; border-radius: 4px; color: #ffcccc;">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<ul class="services-list-links">
    <li><a href="<?= Url::to(['/cabinet/services/gift-code']) ?>"><?= Yii::t('views', 'Подарочный код') ?></a></li>
    <li><a href="/cabinet/services/noble-status"><?= Yii::t('views', 'Статус дворянина') ?></a></li>
    <li><a href="/cabinet/services/remove-karma"><?= Yii::t('views', 'Снять карму') ?></a></li>
    <li><a href="/cabinet/services/change-char-name"><?= Yii::t('views', 'Смена имени') ?></a></li>
    <li><a href="/cabinet/services/change-gender"><?= Yii::t('views', 'Смена пола') ?></a></li>
</ul>