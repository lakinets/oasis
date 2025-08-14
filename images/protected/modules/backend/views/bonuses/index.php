<?php
use yii\helpers\Html;
use yii\widgets\ListView;

echo Html::a('Добавить бонус', ['bonuses/form'], ['class' => 'btn btn-success']);
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_item',
    'summary' => '',
]);