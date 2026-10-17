<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\cabinet\models\UserMessages $model */

$this->title = Yii::t('views', 'Сообщение: {title}', ['title' => $model->title]);
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
    <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
</ul>

<h1><?= Html::encode($model->title) ?></h1>
<div class="card">
    <div class="card-body">
        <p><?= nl2br(Html::encode($model->text)) ?></p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>