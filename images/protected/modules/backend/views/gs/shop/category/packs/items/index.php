<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Предметы в наборе';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="items-index">
    <p>
        <?= Html::a('Добавить предмет', ['shop-category-pack-create-item', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'itemInfo.name',
                'label'     => 'Предмет',
            ],
            'amount',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => fn($url,$m) => Html::a('Edit', ['shop-category-pack-edit-item', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id, 'item_id' => $m->id], ['class' => 'btn btn-xs btn-primary']),
                    'delete' => fn($url,$m) => Html::a('Del', ['shop-category-pack-del-item', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id, 'item_id' => $m->id], [
                        'class' => 'btn btn-xs btn-danger',
                        'data'  => ['confirm' => 'Удалить?', 'method' => 'post'],
                    ]),
                ],
            ],
        ],
    ]) ?>
</div>