<?php
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $categories app\modules\backend\models\ShopCategories[] */

$this->title = 'Категории магазина ' . Html::encode($gs->name);
$this->params['breadcrumbs'][] = ['label' => 'Серверы', 'url' => ['/backend/game-servers']];
$this->params['breadcrumbs'][] = ['label' => $gs->name, 'url' => ['shop', 'gs_id' => $gs->id]];
$this->params['breadcrumbs'][] = 'Категории';
?>
<div class="shop-categories-index">
    <p>
        <?= Html::a('Добавить категорию', ['shop-category-form', 'gs_id' => $gs->id], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="list-group">
        <?php foreach ($categories as $cat): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <?= Html::a(Html::encode($cat->title), ['shop-category-packs', 'gs_id' => $gs->id, 'category_id' => $cat->id]) ?>
                    <br>
                    <small>ID: <?= $cat->id ?> | Статус: <?= $cat->status ? 'Вкл' : 'Выкл' ?></small>
                </div>
                <div>
                    <?= Html::a('Редактировать', ['shop-category-form', 'gs_id' => $gs->id, 'category_id' => $cat->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    <?= Html::a('Удалить', ['shop-category-del', 'gs_id' => $gs->id, 'category_id' => $cat->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'data'  => ['confirm' => 'Удалить?', 'method' => 'post'],
                    ]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>