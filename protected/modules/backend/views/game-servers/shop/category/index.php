<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\Gs $gs */
/** @var app\modules\backend\models\ShopCategories[] $categories */

$this->title = Yii::t('backend', 'Магазин');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Сервера'), 'url' => ['/backend/game-servers/index']];
$this->params['breadcrumbs'][] = $gs->name . ' - ' . $this->title;
?>

<div class="shop-category-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Создать категорию'), ['/backend/game-servers/shop-category-form', 'gs_id' => $gs->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Yii::t('backend', 'Название') ?></th>
                <th width="15%"><?= Yii::t('backend', 'Ссылка') ?></th>
                <th width="15%"><?= Yii::t('backend', 'Кол-во наборов') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Сортировка') ?></th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($categories): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= Html::encode($category->name) ?></td>
                        <td><?= $category->link ?></td>
                        <td><?= $category->countPacks ?></td>
                        <td>
                            <span class="label <?= $category->isStatusOn() ? 'label-success' : 'label-default' ?>">
                                <?= $category->getStatus() ?>
                            </span>
                        </td>
                        <td><?= $category->sort ?></td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                <?= Html::a('', ['/backend/game-servers/shop-category-form', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-default glyphicon glyphicon-pencil', 'title' => Yii::t('backend', 'Редактировать')]) ?>
                                <?= Html::a('', ['/backend/game-servers/shop-category-allow', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-default glyphicon ' . ($category->isStatusOn() ? 'glyphicon-eye-close' : 'glyphicon-eye-open'), 'title' => $category->isStatusOn() ? Yii::t('backend', 'Выключить') : Yii::t('backend', 'Включить')]) ?>
                                <?= Html::a('', ['/backend/game-servers/shop-category-packs', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-default glyphicon glyphicon-th', 'title' => Yii::t('backend', 'Наборы')]) ?>
                                <?= Html::a('', ['/backend/game-servers/shop-category-del', 'gs_id' => $gs->id, 'category_id' => $category->id], ['class' => 'btn btn-danger glyphicon glyphicon-remove', 'title' => Yii::t('backend', 'Удалить'), 'data-confirm' => Yii::t('backend', 'Вы уверены?')]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6"><?= Yii::t('backend', 'Нет данных.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p class="text-danger small">
        <span class="glyphicon glyphicon-exclamation-sign"></span>
        <?= Yii::t('backend', 'Внимание! При удалении категории также удаляются все наборы и все предметы в наборах!') ?>
    </p>
</div>