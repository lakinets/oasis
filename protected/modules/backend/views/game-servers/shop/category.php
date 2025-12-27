<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $packs app\modules\backend\models\ShopItemsPacks[] */
?>

<!-- Создать категорию / Создать пак -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?= Html::encode($category->name) ?></h2>

    <div>
        <?= Html::a(
            'Создать категорию',
            ['/backend/game-servers/shop-category-form', 'gs_id' => $gs->id],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
        <?= Html::a(
            'Создать пак',
            ['/backend/game-servers/shop-pack-form', 'gs_id' => $gs->id, 'category_id' => $category->id],
            ['class' => 'btn btn-primary btn-sm ml-2']
        ) ?>
    </div>
</div>

<!-- Список паков -->
<div class="list-group">
    <?php foreach ($packs as $pack): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <?= Html::a(
                    Html::encode($pack->title),
                    ['/backend/game-servers/shop-pack', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
                    ['class' => 'font-weight-bold']
                ) ?>
            </div>

            <div>
                <?= Html::a(
                    'Редактировать',
                    ['/backend/game-servers/shop-pack-form', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
                    ['class' => 'btn btn-xs btn-warning', 'style' => 'font-size:11px;padding:2px 6px;']
                ) ?>
                <?= Html::a(
                    'Удалить',
                    ['/backend/game-servers/shop-pack-del', 'pack_id' => $pack->id],
                    [
                        'class'   => 'btn btn-xs btn-danger',
                        'style'   => 'font-size:11px;padding:2px 6px;',
                        'data'    => ['confirm' => 'Удалить пак?'],
                    ]
                ) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>