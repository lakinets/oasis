<?php
use yii\widgets\ListView;

$this->title = 'Рефералы';
?>
<h1>Рефералы</h1>
<p>Всего рефералов: <strong><?= $countReferals ?></strong></p>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_referal',
    'summary' => '',
]) ?>