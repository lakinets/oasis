<ul class="nav-mini">
    <li><a href="/cabinet/tickets">Поддержка</a></li>
    <li><a href="/cabinet/characters">Персонажи</a></li>
    <li><a href="/cabinet/shop">Магазин</a></li>
    <li><a href="/cabinet/bonuses">Бонусы</a></li>
    <li><a href="/cabinet/security">Безопасность</a></li>
    <li><a href="/cabinet/messages">Сообщения</a></li>
    <li><a href="/cabinet/deposit">Пополнение</a></li>
    <li><a href="/cabinet/transaction-history">История транзакций</a></li>
    <li><a href="/cabinet/auth-history">История входов</a></li>
    <li><a href="/cabinet/referals">Рефералы</a></li>
    <li><a href="/cabinet/services">Услуги</a></li>
</ul>
<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \app\modules\cabinet\models\ShopCategories $category
 * @var \app\modules\cabinet\models\ShopItemsPacks $pack
 * @var \app\modules\cabinet\models\ShopItems[] $items
 * @var int|null $gs_id
 * @var int|null $char_id
 * @var float|int $balance
 * @var array $characters
 * @var bool $ownerOk
 */

$this->title = $pack->title . ' – ' . $category->name;
?>

<!-- Заголовок + селектор персонажа + баланс -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">

    <div>
        <h3 class="mb-1"><?= Html::encode($pack->title) ?></h3>
        <?php if ($pack->description): ?>
            <p class="text-muted mb-0"><?= nl2br(Html::encode($pack->description)) ?></p>
        <?php endif; ?>
    </div>

    <div class="d-flex align-items-center gap-3">
        <select id="char-selector" class="form-select" style="width:auto;"
                onchange="setChar(this.value)">
            <option value="">— выберите персонажа —</option>
            <?php foreach ($characters as $ch): ?>
                <option value="<?= (int)$ch['char_id'] ?>" <?= (int)$char_id === (int)$ch['char_id'] ? 'selected' : '' ?>>
                    <?= Html::encode($ch['char_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="text-end">
            <div class="small text-muted">Баланс</div>
            <div class="fs-5 fw-semibold"><?= (int)$balance ?> Web Aden</div>
        </div>
    </div>
</div>

<script>
function setChar(id) {
    if (!id) return;
    const url = new URL(window.location);
    url.searchParams.set('char_id', id);
    window.location.href = url.toString();
}
</script>

<!-- Flash-сообщения -->
<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?> alert-dismissible fade show" role="alert">
        <?= Html::encode($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endforeach; ?>

<?php if (empty($items)): ?>
    <div class="alert alert-info">Пак пуст.</div>
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
                    $icon  = ($info && $info->icon && file_exists(Yii::getAlias('@webroot/images/items/' . $info->icon . '.jpg')))
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
                            <button type="button"
                                    class="btn btn-sm btn-primary buy-btn"
                                    data-item-id="<?= $it->id ?>"
                                    data-item-name="<?= Html::encode($title) ?>"
                                    data-price="<?= number_format($final, 2, '.', '') ?>"
                                    <?= (!$gs_id || !$char_id || !$ownerOk) ? 'disabled' : '' ?>>
                                Купить
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Модаль подтверждения покупки -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение покупки</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Купить <b id="itemName"></b> за <span id="itemPrice"></span> Web Aden?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="confirmBuy">Купить</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.buy-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id   = btn.dataset.itemId;
        const name = btn.dataset.itemName;
        const price = btn.dataset.price;

        document.getElementById('itemName').textContent  = name;
        document.getElementById('itemPrice').textContent = price;

        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();

        document.getElementById('confirmBuy').onclick = () => {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '<?= Url::to(['/cabinet/shop/buy-item']) ?>';
            form.innerHTML = `
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" name="item_id" value="${id}">
                <input type="hidden" name="gs_id" value="<?= $gs_id ?>">
                <input type="hidden" name="char_id" value="<?= $char_id ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        };
    });
});
</script>
