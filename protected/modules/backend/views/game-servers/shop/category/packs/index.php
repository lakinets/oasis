<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Gs $gs */
/** @var app\modules\backend\models\ShopCategories $category */
/** @var app\modules\backend\models\ShopItemsPacks[] $packs */
?>

<div class="pack-index">
    <h1><?= Yii::t('backend', 'Наборы в категории: {name}', [
        'name' => Html::encode($category->name)
    ]) ?></h1>

    <p>
        <?= Html::a(
            Yii::t('backend', 'Создать набор'),
            ['/backend/shop/category-pack-form', 'gs_id' => $gs->id, 'category_id' => $category->id],
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Yii::t('backend', 'Название') ?></th>
                <th><?= Yii::t('backend', 'Описание') ?></th>
                <th><?= Yii::t('backend', 'Действия') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($packs as $pack): ?>
                <tr>
                    <td><?= Html::encode($pack->name) ?></td>
                    <td><?= nl2br(Html::encode($pack->description ?? '')) ?></td>
                    <td>
                        <a href="<?= Url::to([
                            '/backend/shop/category-pack-items',
                            'gs_id' => $gs->id,
                            'category_id' => $category->id,
                            'pack_id' => $pack->id
                        ]) ?>" class="btn btn-xs btn-info">
                            <?= Yii::t('backend', 'Предметы') ?>
                        </a>
                        <a href="<?= Url::to([
                            '/backend/shop/category-pack-form',
                            'gs_id' => $gs->id,
                            'category_id' => $category->id,
                            'pack_id' => $pack->id
                        ]) ?>" class="btn btn-xs btn-default">
                            <?= Yii::t('backend', 'Изменить') ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
