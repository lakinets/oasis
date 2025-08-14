<?php
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Наборы в "' . Html::encode($category->title) . '"';
$this->params['breadcrumbs'][] = ['label' => $category->title, 'url' => ['shop', 'gs_id' => $gs->id]];
$this->params['breadcrumbs'][] = 'Наборы';
?>
<div class="packs-index">
    <p>
        <?= Html::a('Добавить набор', ['shop-category-packs-form', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView'     => '_pack',
    ]) ?>
</div>