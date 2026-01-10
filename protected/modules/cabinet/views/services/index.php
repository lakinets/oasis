<?php
use yii\helpers\Html;
use yii\helpers\Url;   // ← эту строку добавили
use app\models\Services;

$this->title = 'Услуги';
?>

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
    <li><a href="/cabinet/services" class="active">Услуги</a></li>
</ul>

<h1>Игровые услуги</h1>

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
    <li><a href="<?= Url::to(['/cabinet/services/gift-code']) ?>">Подарочный код</a></li>
    <li><a href="/cabinet/services/noble-status">Статус дворянина</a></li>
    <li><a href="/cabinet/services/remove-karma">Снять карму</a></li>
    <li><a href="/cabinet/services/change-char-name">Смена имени</a></li>
    <li><a href="/cabinet/services/change-gender">Смена пола</a></li>
</ul>