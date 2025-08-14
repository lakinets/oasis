<?php
/**
 * @var yii\web\View $this
 * @var app\modules\backend\models\TicketsSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $gsList
 * @var array $categories
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

use app\modules\backend\models\Tickets;

$title_ = Yii::t('backend', 'Тикеты');
$this->title = $title_;
$this->params['breadcrumbs'][] = $title_;
?>
<div class="tickets-index">

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type ?>"><?= $message ?></div>
    <?php endforeach; ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Создать тикет'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'tickets-pjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-striped'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '5%'],
            ],
            [
                'attribute' => 'title',
                'label'     => Yii::t('backend', 'Название'),
            ],
            [
                'attribute' => 'category_id',
                'label'     => Yii::t('backend', 'Категория'),
                'filter'    => $categories,
                'value'     => function ($model) use ($categories) {
                    return $categories[$model->category_id] ?? '-';
                },
            ],
            [
                'attribute' => 'priority',
                'label'     => Yii::t('backend', 'Приоритет'),
                'filter'    => $searchModel->getPrioritiesList(),
                'value'     => function ($model) {
                    return $model->getPriority();
                },
            ],
            [
                'attribute' => 'status',
                'label'     => Yii::t('backend', 'Статус'),
                'format'    => 'raw',
                'filter'    => $searchModel->getStatusList(),
                'value'     => function ($model) {
                    $css = $model->isStatusOn() ? 'label-success' : 'label-default';
                    return Html::tag('span', $model->getStatus(), ['class' => "label $css"]);
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
                    $css = $model->new_message_for_admin == Tickets::STATUS_NEW_MESSAGE_ON
                                ? 'label-info' : 'label-default';
                    return Html::tag('span', $model->isNewMessageForAdmin(), ['class' => "label $css"]);
                },
                'headerOptions' => ['width' => '14%'],
            ],
            [
                'attribute' => 'gs_id',
                'label'     => Yii::t('backend', 'Сервер'),
                'filter'    => $gsList,
                'format'    => 'raw',
                'value'     => function ($model) use ($gsList) {
                    return isset($gsList[$model->gs_id])
                        ? Html::a(Html::encode($gsList[$model->gs_id]), ['/backend/game-servers/form', 'id' => $model->gs_id])
                        : '-';
                },
            ],
            [
                'attribute' => 'userLogin',
                'label'     => Yii::t('backend', 'Автор'),
                'format'    => 'raw',
                'value'     => function ($model) {
                    return $model->user
                        ? Html::a(Html::encode($model->user->login), ['/backend/users/view', 'id' => $model->user->user_id])
                        : '-';
                },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons'  => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            ['/backend/tickets/edit', 'id' => $model->id],
                            ['title' => Yii::t('backend', 'Просмотр'), 'data-pjax' => '0']
                        );
                    },
                ],
                'headerOptions' => ['width' => '10%'],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>