<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use app\modules\cabinet\models\ShopItemsPacks;

$this->title = 'Категория: ' . $category->name;
?>
<h1><?= Html::encode($category->name) ?></h1>

<?php
$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => ShopItemsPacks::find()
        ->where(['category_id' => $category->id, 'status' => 1])
        ->orderBy(['sort' => SORT_ASC]),
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_pack',
    'summary' => '',
]) ?>