<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Тикет: ' . $ticket->title;
?>

<h2><?= Html::encode($ticket->title) ?></h2>

<?php if ($ticket->status == 1): ?>
    <?= Html::a('Закрыть тикет', ['/cabinet/tickets/close', 'ticket_id' => $ticket->id], [
        'class' => 'btn btn-warning mb-3',
        'data' => ['confirm' => 'Вы уверены?']
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
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'text')->textarea(['rows' => 4]) ?>
    <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>