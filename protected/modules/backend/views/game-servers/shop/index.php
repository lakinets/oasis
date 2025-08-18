<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $categories app\modules\backend\models\ShopCategories[] */
?>
<h1><?= Yii::t('backend', 'Магазин сервера') ?> <?= Html::encode($gs->name) ?></h1>

<div class="d-flex mb-3">
    <?= Html::a(
        'Создать категорию',
        ['/backend/game-servers/shop-category-form', 'gs_id' => $gs->id],
        ['class' => 'btn btn-success mr-2']
    ) ?>
</div>

<div class="list-group">
    <?php foreach ($categories as $category): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <?= Html::a(
                    Html::encode($category->name),
                    ['/backend/game-servers/shop-category', 'gs_id' => $gs->id, 'category_id' => $category->id],
                    ['class' => 'font-weight-bold']
                ) ?>
            </div>
            <div>
                <?= Html::a(
                    'Удалить',
                    ['/backend/game-servers/shop-category-del', 'category_id' => $category->id],
                    [
                        'class' => 'btn btn-danger btn-sm',
                        'data-confirm' => 'Удалить категорию?',
                        'data-method' => 'post',
                    ]
                ) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
