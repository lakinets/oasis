<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $gs app\modules\backend\models\Gs */
/* @var $category app\modules\backend\models\ShopCategories */
/* @var $pack app\modules\backend\models\ShopItemsPacks */
/* @var $items app\modules\backend\models\ShopItems[] */
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= Html::encode($pack->title) ?></h3>
    <?= Html::a(
        'Добавить предмет',
        ['/backend/game-servers/shop-item-form', 'gs_id' => $gs->id, 'category_id' => $category->id, 'pack_id' => $pack->id],
        ['class' => 'btn btn-success']
    ) ?>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th><?= Yii::t('backend', 'Предмет') ?></th>
            <th><?= Yii::t('backend', 'Описание') ?></th>
            <th><?= Yii::t('backend', 'Цена') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <?php $info = $item->itemInfo; ?>
            <tr>
                <td>
                    <?php
                    $baseName = $info ? $info->icon : '';
                    $iconPath = '/images/items/' . ($baseName ? $baseName . '.jpg' : 'no-image.jpg');
                    $iconFull = Yii::getAlias('@webroot') . $iconPath;
                    $src = is_file($iconFull) ? $iconPath : '/images/items/no-image.jpg';
                    ?>
                    <?= Html::img($src, [
                        'alt'   => $info ? $info->name : 'Предмет',
                        'class' => 'img-rounded',
                        'width' => 32,
                        'height'=> 32,
                    ]) ?>
                    <span class="ml-2"><?= Html::encode($info ? $info->name : $item->item_id) ?></span>
                </td>
                <td><?= nl2br(Html::encode($info ? $info->description : $item->description)) ?></td>
                <td><?= $item->cost ?> Web Adena</td>
                <td>
                    <?= Html::a(
                        'Удалить',
                        ['/backend/game-servers/shop-item-del', 'item_id' => $item->id],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'data-confirm' => 'Точно удалить?',
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$js = <<<JS
$('.buy-btn').on('click', function () {
    const btn = $(this);
    $.post('/backend/game-servers/buy', {
        _csrf: yii.getCsrfToken(),
        item_id: btn.data('item-id'),
        gs_id: btn.data('gs-id'),
        player_id: btn.data('player-id')
    }, function (res) {
        if (res.success) {
            alert('Предмет добавлен!');
        } else {
            alert(res.error || 'Ошибка');
        }
    });
});
JS;
$this->registerJs($js);
?>