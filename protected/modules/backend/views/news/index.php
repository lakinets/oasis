<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Новости';
?>
<p><?= Html::a('Добавить новость', ['form'], ['class' => 'btn btn-success']) ?></p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        'id',
        'title',
        [
            'attribute' => 'status',
            'value'     => fn($m) => $m->getStatusLabel(),
            'filter'    => [1 => 'Активна', 0 => 'Не активна'],
        ],
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {allow} {del}',
            'buttons' => [
                'view'   => fn($url,$m) => Html::a('Просмотр', ['form', 'id' => $m->id], ['class' => 'btn btn-xs btn-primary']),
                'update' => fn($url,$m) => Html::a('Изменить', ['form', 'id' => $m->id], ['class' => 'btn btn-xs btn-warning']),
                'allow'  => fn($url,$m) => Html::a('Статус', ['allow', 'id' => $m->id], [
                    'class' => 'btn btn-xs btn-info',
                    'data'  => ['confirm' => 'Изменить статус?', 'method' => 'post'],
                ]),
                'del'    => fn($url,$m) => Html::a('Удалить', ['del', 'id' => $m->id], [
                    'class' => 'btn btn-xs btn-danger',
                    'data'  => ['confirm' => 'Удалить?', 'method' => 'post'],
                ]),
            ],
        ],
    ],
]) ?>