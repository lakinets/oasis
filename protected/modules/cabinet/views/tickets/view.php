<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Тикет: ' . $ticket->title;
?>

<h2><?= Html::encode($ticket->title) ?></h2>

<p>
    <strong>Статус:</strong>
    <?php if ($ticket->status == 1): ?>
        <span class="badge bg-success">Открыт</span>
    <?php else: ?>
        <span class="badge bg-danger">Закрыт</span>
    <?php endif; ?>
</p>

<?php if ($ticket->status == 1): ?>
    <?= Html::a('Закрыть тикет', ['/cabinet/tickets/close', 'ticket_id' => $ticket->id], [
        'class' => 'btn btn-warning mb-3',
        'data' => ['confirm' => 'Вы уверены, что хотите закрыть тикет?']
    ]) ?>
<?php endif; ?>

<h4>История сообщений</h4>
<?= \yii\widgets\ListView::widget([
    'dataProvider' => $answersDataProvider,
    'itemView' => '_answer',
    'summary' => '',
]) ?>

<?php if ($ticket->status == 1): ?>
    <h5 class="mt-4">Ответить</h5>
    <?php $form = ActiveForm::begin([
        'action' => ['/cabinet/tickets/reply', 'ticket_id' => $ticket->id],
        'method' => 'post',
    ]); ?>
        <?= Html::hiddenInput('ticket_id', $ticket->id) ?>
        <?= $form->field($model, 'text')->textarea(['rows' => 4])->label('Сообщение') ?>
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>