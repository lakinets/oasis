<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $pack app\modules\backend\models\ShopItemsPacks */
/* @var $items app\modules\backend\models\ShopItems[] */
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= Html::encode($pack->getTitle()) ?></h3>
    <?= Html::a(
        Yii::t('backend', 'Добавить предмет'),
        ['/backend/game-servers/shop-item-form', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
        ['class' => 'btn btn-success']
    ) ?>
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th><?= Yii::t('backend', 'Предмет') ?></th>
            <th><?= Yii::t('backend', 'Описание') ?></th>
            <th><?= Yii::t('backend', 'Кол-во') ?></th>
            <th><?= Yii::t('backend', 'Цена') ?></th>
            <th><?= Yii::t('backend', 'Скидка') ?></th>
            <th><?= Yii::t('backend', 'Статус') ?></th>
            <th><?= Yii::t('backend', 'Действия') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <?php
                $info   = $item->itemInfo;
                $descr  = $item->getDescription();
                $status = $item->status ? Yii::t('backend', 'Активен') : Yii::t('backend', 'Неактивен');
            ?>
            <tr>
                <td>
                    <?php
                    $iconFile = $info && $info->icon
                        ? Yii::getAlias('@webroot/images/items/' . $info->icon . '.jpg')
                        : null;

                    $src = is_file($iconFile)
                        ? '/images/items/' . $info->icon . '.jpg'
                        : '/images/items/no-image.jpg';
                    ?>
                    <?= Html::img($src, [
                        'alt'    => $info ? $info->name : Yii::t('backend', 'Предмет'),
                        'class'  => 'img-rounded',
                        'width'  => 32,
                        'height' => 32,
                    ]) ?>
                    <span class="ml-2"><?= Html::encode($info ? $info->name : $item->item_id) ?></span>
                </td>
                <td><?= nl2br(Html::encode($descr)) ?></td>
                <td><?= $item->count ?></td>
                <td><?= $item->cost ?> Web Adena</td>
                <td><?= $item->discount ?> %</td>
                <td><?= $status ?></td>
                <td>
                    <?= Html::a(
                        Yii::t('backend', 'Изменить'),
                        [
                            '/backend/game-servers/shop-item-form',
                            'item_id'     => $item->id,
                            'gs_id'       => $gs->id,
                            'category_id' => $category->id,
                            'pack_id'     => $pack->id,
                        ],
                        ['class' => 'btn btn-xs btn-warning', 'style' => 'font-size:11px;padding:2px 6px;']
                    ) ?>

                    <?= Html::a(
                        $item->status ? Yii::t('backend', 'Откл.') : Yii::t('backend', 'Вкл.'),
                        ['/backend/game-servers/shop-item-toggle', 'item_id' => $item->id],
                        ['class' => 'btn btn-xs ' . ($item->status ? 'btn-danger' : 'btn-success'), 'style' => 'font-size:11px;padding:2px 6px;']
                    ) ?>

                    <?= Html::a(
                        Yii::t('backend', 'Удалить'),
                        ['/backend/game-servers/shop-item-del', 'item_id' => $item->id],
                        ['class' => 'btn btn-xs btn-danger', 'style' => 'font-size:11px;padding:2px 6px;', 'data-confirm' => Yii::t('backend', 'Удалить?')]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>