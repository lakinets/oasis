<?php
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Галерея';
?>
<p><?= Html::a('Добавить картинку', ['form'], ['class' => 'btn btn-success']) ?></p>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_item',
]) ?>