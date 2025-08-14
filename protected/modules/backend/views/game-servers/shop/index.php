<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $categories app\modules\backend\models\ShopCategories[] */
?>
<h1><?= Yii::t('backend', 'Магазин сервера') ?> <?= $gs->name ?></h1>
<div class="list-group">
    <?php foreach ($categories as $category): ?>
        <?= Html::a($category->name,
            ['/backend/game-servers/shop-category', 'gs_id' => $gs->id, 'category_id' => $category->id],
            ['class' => 'list-group-item']) ?>
    <?php endforeach; ?>
</div>