<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Login-серверы';
?>
<p><?= Html::a('Добавить', ['form'], ['class' => 'btn btn-success']) ?></p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        'id',
        'name',
        'host',
        'port',
        [
            'attribute' => 'status',
            'value'     => fn($m) => $m->getStatusLabel(),
            'filter'    => [1 => 'Активен', 0 => 'Не активен'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {allow} {del}',
            'buttons' => [
                'view'   => fn($url,$m) => Html::a('Просмотр', ['form', 'ls_id' => $m->id], ['class' => 'btn btn-xs btn-primary']),
                'allow'  => fn($url,$m) => Html::a('Статус', ['allow', 'ls_id' => $m->id], ['class' => 'btn btn-xs btn-info']),
                'del'    => fn($url,$m) => Html::a('Удалить', ['del', 'ls_id' => $m->id], ['class' => 'btn btn-xs btn-danger']),
            ],
        ],
    ],
]) ?>