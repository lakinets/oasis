<?php
/**
 * @var yii\web\View $this
 * @var app\modules\backend\models\Tickets $ticket
 * @var app\modules\backend\models\TicketsAnswers $answerModel
 * @var yii\data\ActiveDataProvider $answersDataProvider
 * @var array $gsList
 * @var array $categories
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = Yii::t('backend', 'Тикеты');
$this->params['breadcrumbs'] = [
    ['label' => $this->title, 'url' => ['index']],
    $ticket->title . ' - ' . Yii::t('backend', 'Просмотр'),
];
?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<h3>Информация о тикете</h3>
<table class="table table-bordered">
    <tbody>
    <tr>
        <td width="30%"><?= Yii::t('backend', 'Автор') ?></td>
        <td width="70%">
            <?= Html::a($ticket->user->login,
                ['/backend/users/view', 'id' => $ticket->user->id],
                ['title' => Yii::t('backend', 'Перейти к просмотру пользователя'), 'target' => '_blank'])
            ?>
        </td>
    </tr>
    <tr>
        <td><?= Yii::t('backend', 'Категория') ?></td>
        <td><?= Html::encode($ticket->category->title) ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('backend', 'Приоритет') ?></td>
        <td><?= $ticket->getPriority() ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('backend', 'Дата инцидента') ?></td>
        <td><?= Html::encode($ticket->date_incident) ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('backend', 'Имя персонажа') ?></td>
        <td><?= Html::encode($ticket->char_name) ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('backend', 'Дата создания тикета') ?></td>
        <td><?= Yii::$app->formatter->asDatetime($ticket->created_at) ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('backend', 'Дата последнего ответа') ?></td>
        <td><?= Yii::$app->formatter->asDatetime($ticket->updated_at) ?></td>
    </tr>
    </tbody>
</table>

<h3><?= Yii::t('backend', 'Ответы') ?></h3>
<hr>

<?php if ($answers = $answersDataProvider->models): ?>
    <?php foreach ($answers as $answer): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><?= Yii::t('backend', 'Дата') ?>:</strong>
                <?= Yii::$app->formatter->asDatetime($answer->created_at) ?>
                &nbsp;&nbsp;
                <strong><?= Yii::t('backend', 'Автор') ?>:</strong>
                <?= Html::a($answer->userInfo->login,
                    ['/backend/users/view', 'id' => $answer->userInfo->id],
                    ['target' => '_blank'])
                ?>
                <span class="label label-info">(<?= $answer->userInfo->role ?>)</span>
            </div>
            <div class="panel-body">
                <?= nl2br(Html::encode($answer->text)) ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?= LinkPager::widget(['pagination' => $answersDataProvider->pagination]) ?>
<?php else: ?>
    <p class="text-muted"><?= Yii::t('backend', 'Нет данных.') ?></p>
<?php endif; ?>

<h3><?= Yii::t('backend', 'Добавить ответ') ?></h3>

<?php if ((int)$ticket->status === \app\modules\backend\models\Tickets::STATUS_OPEN): ?>
    <?php $form = ActiveForm::begin([
        'id'      => 'ticket-answer-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-offset-3 col-lg-9\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-3 control-label'],
        ],
    ]) ?>

    <?= $form->field($answerModel, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton(Yii::t('backend', 'Добавить ответ'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>
<?php else: ?>
    <div class="alert alert-warning"><?= Yii::t('backend', 'Нельзя добавить ответ в закрытый тикет.') ?></div>
<?php endif; ?>
