<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Gs $gs */
/** @var app\modules\backend\models\ShopCategories $category */
$packs = $category->getPacks()->orderBy(['sort' => SORT_ASC])->all();
?>
<div class="shop-category">
    <h1><?= Html::encode($category->name) ?></h1>
    <p>
        <?= Html::a(Yii::t('backend', 'Создать набор'), ['/backend/shop-pack/form', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-success']) ?>
    </p>

    <table class="table table-bordered">
        <thead>
            <tr><th>Название</th><th>Описание</th><th>Действия</th></tr>
        </thead>
        <tbody>
            <?php foreach ($packs as $pack): ?>
                <tr>
                    <td><?= Html::encode($pack->title) ?></td>
                    <td><?= nl2br(Html::encode($pack->description)) ?></td>
                    <td>
                        <?= Html::a('Предметы', ['/backend/shop-pack-items', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id], ['class' => 'btn btn-xs btn-info']) ?>
                        <?= Html::a('Изменить', ['/backend/shop-pack/form', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id], ['class' => 'btn btn-xs btn-default']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>