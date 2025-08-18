<?php
use yii\widgets\ListView;

$this->title = 'Категория: ' . $categoryModel->name;
?>
<h1><?= Html::encode($categoryModel->name) ?></h1>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_pack',
    'summary' => '',
]) ?>