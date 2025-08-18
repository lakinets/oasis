<?php
use yii\widgets\ListView;

$this->title = 'Сообщения';
?>
<h1>Сообщения</h1>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_message',
    'summary' => '',
]) ?>