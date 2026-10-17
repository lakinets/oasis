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
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('views', 'Тикет: {title}', [
    'title' => $ticket->title,
]);
?>

<h2><?= Html::encode($ticket->title) ?></h2>

<p>
    <strong><?= Yii::t('views', 'Статус:') ?></strong>
    <?php if ($ticket->status == 1): ?>
        <span class="badge bg-success"><?= Yii::t('views', 'Открыт') ?></span>
    <?php else: ?>
        <span class="badge bg-danger"><?= Yii::t('views', 'Закрыт') ?></span>
    <?php endif; ?>
</p>

<?php if ($ticket->status == 1): ?>
    <?= Html::a(Yii::t('views', 'Закрыть тикет'), ['/cabinet/tickets/close', 'ticket_id' => $ticket->id], [
        'class' => 'btn btn-warning mb-3',
        'data' => ['confirm' => Yii::t('views', 'Вы уверены, что хотите закрыть тикет?')]
    ]) ?>
<?php endif; ?>

<h4><?= Yii::t('views', 'История сообщений') ?></h4>
<?= \yii\widgets\ListView::widget([
    'dataProvider' => $answersDataProvider,
    'itemView' => '_answer',
    'summary' => '',
]) ?>

<?php if ($ticket->status == 1): ?>
    <h5 class="mt-4"><?= Yii::t('views', 'Ответить') ?></h5>
    <?php $form = ActiveForm::begin([
        'action' => ['/cabinet/tickets/reply', 'ticket_id' => $ticket->id],
        'method' => 'post',
    ]); ?>
        <?= Html::hiddenInput('ticket_id', $ticket->id) ?>
        <?= $form->field($model, 'text')->textarea(['rows' => 4])->label(Yii::t('views', 'Сообщение')) ?>
        <?= Html::submitButton(Yii::t('views', 'Отправить'), ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>
