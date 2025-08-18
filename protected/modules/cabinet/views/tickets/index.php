<?php
use yii\widgets\ListView;

$this->title = 'Мои тикеты';
?>
<h1>Мои тикеты</h1>

<div class="text-right mb-3">
    <a href="/cabinet/tickets/add" class="btn btn-primary">Создать тикет</a>
</div>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_ticket',
    'summary' => '',
]) ?>