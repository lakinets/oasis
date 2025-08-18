<?php
use yii\widgets\ListView;

$this->title = 'История транзакций';
?>
<h1>История транзакций</h1>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_transaction',
    'summary' => '',
]) ?>