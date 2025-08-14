<?php
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'Тикеты';
?>
<p><?= Html::a('Категории', ['categories'], ['class' => 'btn btn-info']) ?></p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        'id',
        'title',
        ['attribute' => 'user.login', 'label' => 'Автор'],
        ['attribute' => 'status', 'value' => 'statusLabel'],
    ],
]) ?>