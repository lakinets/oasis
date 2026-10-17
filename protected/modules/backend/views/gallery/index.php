<?php
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = Yii::t('backend', 'Галерея');
?>
<p><?= Html::a(Yii::t('backend', 'Добавить картинку'), ['form'], ['class' => 'btn btn-success']) ?></p>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_item',
]) ?>
