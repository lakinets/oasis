<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Мои тикеты';
?>
<h2 class="orion-table-header"><?= Html::encode($this->title) ?></h2>

<?= Html::a(
    'Создать тикет',
    ['/cabinet/tickets/add'],
    ['class' => 'btn btn-orion mb-3']
) ?>

<div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
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
                            ['class' => 'btn btn-orion-sm']
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>