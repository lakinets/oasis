<?php
/**
 * @var \app\modules\cabinet\models\ShopCategories $category
 * @var \app\modules\cabinet\models\ShopItems[]   $items
 * @var \app\models\Gs[]                         $servers
 * @var int|null                                 $gs_id
 * @var array                                    $characters
 * @var int|null                                 $char_id
 * @var float|int                                $balance
 */
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\cabinet\models\ShopItems;

$this->title = 'Тип товара: ' . $category->name;
?>

<h1 class="mb-3"><?= Html::encode($this->title) ?></h1>

<?= $this->render('_selector', [
    'servers'       => $servers,
    'gs_id'         => $gs_id,
    'characters'    => $characters,
    'char_id'       => $char_id ?? null,
    'category_link' => $category->link,
    'balance'       => $balance,
]) ?>

<?php
// вытаскиваем все активные предметы категории (через pack_id = category_id)
$items = ShopItems::find()
    ->alias('si')
    ->innerJoin('shop_items_packs sip', 'sip.id = si.pack_id')
    ->where(['sip.category_id' => $category->id, 'si.status' => 1])
    ->orderBy(['si.sort' => SORT_ASC])
    ->all();
?>

<?php if (empty($items)): ?>
    <div class="alert alert-info">В этом типе товара пока нет предложений.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Предмет</th>
                    <th class="text-end">Кол-во</th>
                    <th class="text-end">Заточка</th>
                    <th class="text-end">Цена</th>
                    <th class="text-end">Скидка</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it):
                    $info  = $it->itemInfo;
                    $title = $info->name ?? ('ID: ' . $it->item_id);
                    $icon = ($info && $info->icon && file_exists(Yii::getAlias('@webroot/images/items/' . $info->icon . '.jpg')))
					? '/images/items/' . $info->icon . '.jpg'
					: '/images/items/no-image.jpg';
                    $final = round((float)$it->cost * (1 - max(0, min((float)($it->discount ?? 0), 100)) / 100), 2);
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?= Html::encode($icon) ?>" width="32" height="32" alt="">
                                <div>
                                    <div class="fw-semibold"><?= Html::encode($title) ?></div>
                                    <?php if ($it->description): ?>
                                        <div class="small text-muted"><?= nl2br(Html::encode($it->description)) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-end"><?= (int)$it->count ?></td>
                        <td class="text-end">+<?= (int)$it->enchant ?></td>
                        <td class="text-end"><?= number_format($it->cost, 2, '.', ' ') ?></td>
                        <td class="text-end"><?= number_format($it->discount, 0, '.', '') ?> %</td>
                        <td class="text-end">
                            <form method="post" action="<?= Url::to(['/cabinet/shop/buy']) ?>">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                                <?= Html::hiddenInput('item_id', $it->id) ?>
                                <?= Html::hiddenInput('gs_id', $gs_id) ?>
                                <?= Html::hiddenInput('char_id', $char_id) ?>
                                <button class="btn btn-sm btn-primary"
                                        <?= (!$gs_id || !$char_id) ? 'disabled' : '' ?>>
                                    Купить
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>