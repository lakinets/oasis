<?php
/**
 * @var yii\web\View $this
 * @var app\modules\backend\models\TicketsSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $gsList
 * @var array $categories
 */

use yii\helpers\Html;
use yii\widgets\Pjax;
use app\modules\backend\models\Tickets;

$title_ = Yii::t('backend', 'Тикеты');
$this->title = $title_;
$this->params['breadcrumbs'][] = $title_;
?>
<div class="tickets-index">

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Создать тикет'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'tickets-pjax']); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'id', 'headerOptions' => ['width' => '5%']],
            [
                'attribute' => 'title',
                'label' => Yii::t('backend', 'Название'),
                'format' => 'text'
            ],
            [
                'attribute' => 'category_id',
                'label'     => Yii::t('backend', 'Категория'),
                'filter'    => $categories,
                'value'     => fn($model) => $categories[$model->category_id] ?? '-',
            ],
            [
                'attribute' => 'priority',
                'label'     => Yii::t('backend', 'Приоритет'),
                'filter'    => Tickets::getPriorityList(),
                'value'     => fn($model) => $model->getPriorityLabel(),
            ],
            [
                'attribute' => 'status',
                'label'     => Yii::t('backend', 'Статус'),
                'format'    => 'raw',
                'filter'    => Tickets::getStatusList(),
                'value'     => function ($model) {
                    $css = $model->isStatusOn() ? 'badge bg-success' : 'badge bg-secondary';
                    return Html::tag('span', $model->getStatusLabel(), ['class' => $css]);
                },
            ],
            [
                'attribute' => 'new_message_for_admin',
                'label'     => Yii::t('backend', 'Новые сообщения'),
                'format'    => 'raw',
                'filter'    => [
                    Tickets::STATUS_NEW_MESSAGE_OFF => Yii::t('backend', 'Нет'),
                    Tickets::STATUS_NEW_MESSAGE_ON  => Yii::t('backend', 'Да'),
                ],
                'value'     => function ($model) {
                    $isOn = $model->isNewMessageForAdmin();
                    $css  = $isOn ? 'badge bg-info' : 'badge bg-secondary';
                    return Html::tag('span', $isOn ? Yii::t('backend','Да') : Yii::t('backend','Нет'), ['class' => $css]);
                },
                'headerOptions' => ['width' => '14%'],
            ],
            [
                'attribute' => 'gs_id',
                'label'     => Yii::t('backend', 'Сервер'),
                'filter'    => $gsList,
                'format'    => 'raw',
                'value'     => fn($model) => isset($gsList[$model->gs_id])
                    ? Html::a(Html::encode($gsList[$model->gs_id]), ['/backend/game-servers/form', 'id' => $model->gs_id])
                    : '-',
            ],
            [
                'attribute' => 'userLogin',
                'label'     => Yii::t('backend', 'Автор'),
                'format'    => 'raw',
                'value'     => fn($model) => $model->user
                    ? Html::a(Html::encode($model->user->login), ['/backend/users/view', 'id' => $model->user->user_id])
                    : '-',
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'header'   => Yii::t('backend', 'Действия'),
                'template' => '{update} {toggle} {delete}',
                'buttons'  => [
                    'update' => function ($url, $model) {
                        return Html::a(
                            '<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('backend', 'Ред.'),
                            ['/backend/tickets/edit', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-warning',
                                'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;'
                            ]
                        );
                    },
                    'toggle' => function ($url, $model) {
                        return Html::a(
                            '<i class="glyphicon ' . ($model->status === Tickets::STATUS_OPEN ? 'glyphicon-eye-close' : 'glyphicon-eye-open') . '"></i> ' .
                            ($model->status === Tickets::STATUS_OPEN ? Yii::t('backend', 'Деакт.') : Yii::t('backend', 'Актив.')),
                            ['/backend/tickets/toggle', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs ' . ($model->status === Tickets::STATUS_OPEN ? 'btn-danger' : 'btn-success'),
                                'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                'data'  => [
                                    'confirm' => Yii::t('backend', 'Изменить статус тикета?'),
                                    'method'  => 'post',
                                ],
                            ]
                        );
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(
                            '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('backend', 'Удал.'),
                            ['/backend/tickets/delete', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-danger',
                                'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                'data-confirm' => Yii::t('backend', 'Вы уверены, что хотите удалить тикет?'),
                                'data-method'  => 'post',
                            ]
                        );
                    },
                ],
                'headerOptions' => ['class' => 'text-center', 'width' => '20%'],
                'contentOptions' => ['class' => 'text-center'],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>