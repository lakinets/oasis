<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('backend', 'Новости');
?>
<p><?= Html::a(Yii::t('backend', 'Добавить новость'), ['form'], ['class' => 'btn btn-success']) ?></p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        'id',
        'title',
        [
            'attribute' => 'status',
            'value'     => fn($m) => $m->getStatusLabel(),
            'filter'    => [1 => Yii::t('backend', 'Активна'), 0 => Yii::t('backend', 'Не активна')],
        ],
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {allow} {del}',
            'buttons' => [
                'view'   => fn($url,$m) => Html::a(Yii::t('backend', 'Просмотр'), ['form', 'id' => $m->id], ['class' => 'btn btn-xs btn-primary']),
                'update' => fn($url,$m) => Html::a(Yii::t('backend', 'Изменить'), ['form', 'id' => $m->id], ['class' => 'btn btn-xs btn-warning']),
                'allow'  => fn($url,$m) => Html::a(Yii::t('backend', 'Статус'), ['allow', 'id' => $m->id], [
                    'class' => 'btn btn-xs btn-info',
                    'data'  => ['confirm' => Yii::t('backend', 'Изменить статус?'), 'method' => 'post'],
                ]),
                'del'    => fn($url,$m) => Html::a(Yii::t('backend', 'Удалить'), ['del', 'id' => $m->id], [
                    'class' => 'btn btn-xs btn-danger',
                    'data'  => ['confirm' => Yii::t('backend', 'Удалить?'), 'method' => 'post'],
                ]),
            ],
        ],
    ],
]) ?>
