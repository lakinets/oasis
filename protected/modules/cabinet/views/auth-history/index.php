<?php
use yii\widgets\ListView;

$this->title = 'История входов';
?>
<h1><?= $this->title ?></h1>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_auth',   
    'summary'   => '',
]) ?>
