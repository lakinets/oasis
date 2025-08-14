<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $packs app\modules\backend\models\ShopItemsPacks[] */
?>
<h2><?= $category->name ?></h2>
<div class="list-group">
    <?php foreach ($packs as $pack): ?>
        <?= Html::a($pack->title,
            ['/backend/game-servers/shop-pack', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
            ['class' => 'list-group-item']) ?>
    <?php endforeach; ?>
</div>