<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Серверы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gs-index">
    <p><?= Html::a('Добавить сервер', ['form'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'status',
                'value'     => fn($m) => $m->getStatusLabel(),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view'   => fn($url,$m) => Html::a('Shop', ['shop', 'gs_id' => $m->id], ['class' => 'btn btn-xs btn-info']),
                    'update' => fn($url,$m) => Html::a('Edit', ['form', 'gs_id' => $m->id], ['class' => 'btn btn-xs btn-primary']),
                    'delete' => fn($url,$m) => Html::a('Del', ['del', 'gs_id' => $m->id], [
                        'class' => 'btn btn-xs btn-danger',
                        'data'  => ['confirm' => 'Удалить?', 'method' => 'post'],
                    ]),
                ],
            ],
        ],
    ]) ?>
</div>