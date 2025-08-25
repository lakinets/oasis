<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Мои тикеты';
?>

<h2><?= Html::encode($this->title) ?></h2>

<?= Html::a(
    'Создать тикет',
    ['/cabinet/tickets/add'],
    ['class' => 'btn btn-success mb-3']
) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->status == 1
                    ? '<span class="badge bg-success">Открыт</span>'
                    : '<span class="badge bg-danger">Закрыт</span>';
            },
        ],
        [
            'attribute' => 'created_at',
            'format' => ['datetime', 'php:d.m.Y H:i'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a(
                        'Открыть',
                        ['/cabinet/tickets/view', 'ticket_id' => $model->id],
                        ['class' => 'btn btn-primary btn-sm']
                    );
                },
            ],
        ],
    ],
]); ?>