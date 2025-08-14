<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\modules\backend\models\Gs $gs */
/** @var app\modules\backend\models\ShopCategories $category */
/** @var app\modules\backend\models\ShopItemsPacks $pack */

$this->title = Yii::t('backend', 'Предметы в наборе - :pack_name', ['pack_name' => $pack->title]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Сервера'), 'url' => ['/backend/game-servers/index']];
$this->params['breadcrumbs'][] = [
    'label' => $gs->name . ' - ' . Yii::t('backend', 'Магазин'),
    'url' => ['/backend/game-servers/shop', 'gs_id' => $gs->id]
];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('backend', 'Наборы для категории - :category_name', ['category_name' => $category->name]),
    'url' => ['/backend/game-servers/shop-category-packs', 'gs_id' => $gs->id, 'category_id' => $category->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pack-items">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Добавить предмет'), ['/backend/game-servers/shop-category-pack-create-item', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="5%"></th>
                <th><?= Yii::t('backend', 'Название') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Стоимость') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Скидка') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Кол-во') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Заточка') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($dataProvider->models): ?>
                <?php foreach ($dataProvider->models as $item): ?>
                    <tr>
                        <td><?= $item->itemInfo->getIcon() ?></td>
                        <td>
                            <?= Html::encode($item->itemInfo->name) ?> (<?= $item->itemInfo->item_id ?>)
                            <?php if ($item->itemInfo->add_name): ?>
                                <br><small><?= Html::encode($item->itemInfo->add_name) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= Yii::$app->formatter->asInteger($item->cost) ?></td>
                        <td><?= Yii::$app->formatter->asInteger($item->discount) ?></td>
                        <td><?= Yii::$app->formatter->asInteger($item->count) ?></td>
                        <td><?= Yii::$app->formatter->asInteger($item->enchant) ?></td>
                        <td><?= $item->getStatus() ?></td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                <?= Html::a('', ['/backend/game-servers/shop-category-pack-edit-item', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id, 'item_id' => $item->id], ['class' => 'btn btn-default glyphicon glyphicon-pencil', 'title' => Yii::t('backend', 'Редактировать')]) ?>
                                <?= Html::a('', ['/backend/game-servers/shop-category-pack-del-item', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id, 'item_id' => $item->id], ['class' => 'btn btn-danger glyphicon glyphicon-remove', 'title' => Yii::t('backend', 'Удалить'), 'data-confirm' => Yii::t('backend', 'Вы уверены?')]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8"><?= Yii::t('backend', 'Нет данных.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>