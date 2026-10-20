<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

/* @var $servers app\models\Gs[] */
/* @var $gs app\models\Gs */
/* @var $gs_id int */
/* @var $char_id int|null */
/* @var $characters array */
/* @var $ownerOk bool */
/* @var $config app\modules\backend\models\ShopTopupConfig */
/* @var $tiers app\modules\backend\models\ShopTopupTier[] */
/* @var $providers array */

$this->title = Yii::t('views', 'Пополнение баланса');
$isEn = (Yii::$app->language === 'en');
$desc = $isEn ? $config->description_en : $config->description_ru;

// Передаем пороги в JS
$tiersData = [];
foreach ($tiers as $t) {
    $tiersData[] = [
        'min' => (int)$t->min_qty,
        'max' => (int)$t->max_qty,
        'bonus' => (int)$t->bonus_qty,
    ];
}
$tiersJson = Json::encode($tiersData);
$price = (float)$config->price;
$isReady = ($char_id && $ownerOk);
?>

<!-- Добавляем стили, чтобы принудительно выровнять селекты по левому краю -->
<style>
    .topup-select {
        width: 100% !important;
        text-align: left !important;
        margin-left: 0 !important;
        padding-left: 15px !important;
    }
    .topup-label {
        display: block;
        text-align: left !important;
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
</style>

<ul class="nav-mini">
    <li><a href="/cabinet/tickets"><?= Yii::t('views', 'Поддержка') ?></a></li>
    <li><a href="/cabinet/characters"><?= Yii::t('views', 'Персонажи') ?></a></li>
    <li><a href="/cabinet/shop"><?= Yii::t('views', 'Магазин') ?></a></li>
    <li><a href="/cabinet/bonuses"><?= Yii::t('views', 'Бонусы') ?></a></li>
    <li><a href="/cabinet/security"><?= Yii::t('views', 'Безопасность') ?></a></li>
    <li><a href="/cabinet/messages"><?= Yii::t('views', 'Сообщения') ?></a></li>
    <li><a href="/cabinet/deposit"><?= Yii::t('views', 'Пополнение') ?></a></li>
    <li><a href="/cabinet/transaction-history"><?= Yii::t('views', 'История транзакций') ?></a></li>
    <li><a href="/cabinet/auth-history"><?= Yii::t('views', 'История входов') ?></a></li>
    <li><a href="/cabinet/referals"><?= Yii::t('views', 'Рефералы') ?></a></li>
    <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
</ul>

<h1 class="mb-4"><?= Yii::t('views', 'Пополнение баланса') ?></h1>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?> alert-dismissible fade show" role="alert">
        <?= Html::encode($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>

<div class="row">
    <!-- Информация о предмете -->
    <div class="col-md-5 mb-4">
        <div class="p-4 h-100 rounded" style="border: 1px solid rgba(255,255,255,0.1);">
            <h4 class="mb-3"><?= Html::encode($config->item_name) ?></h4>
            <?php if ($desc): ?>
                <p class="text-muted mb-4"><?= nl2br(Html::encode($desc)) ?></p>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between mb-2 border-bottom pb-2" style="border-color: rgba(255,255,255,0.1) !important;">
                <span class="text-muted"><?= Yii::t('views', 'Количество в пакете') ?></span>
                <b><?= (int)$config->item_count ?></b>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted"><?= Yii::t('views', 'Цена за 1 шт.') ?></span>
                <b class="text-warning"><?= number_format($price, 2) ?> <?= Html::encode($config->currency) ?></b>
            </div>
        </div>
    </div>

    <!-- Форма покупки -->
    <div class="col-md-7 mb-4">
        <div class="p-4 h-100 rounded" style="border: 1px solid rgba(255,255,255,0.1);">
            <form method="get" action="<?= Url::to(['/cabinet/shop/topup']) ?>" class="mb-4">
                <input type="hidden" name="gs_id" value="<?= $gs_id ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small topup-label"><?= Yii::t('views', 'Сервер') ?></label>
                        <select name="gs_id" class="form-select topup-select" onchange="this.form.submit()">
                            <?php foreach ($servers as $s): ?>
                                <option value="<?= (int)$s->id ?>" <?= $gs_id === (int)$s->id ? 'selected' : '' ?>>
                                    <?= Html::encode($s->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small topup-label"><?= Yii::t('views', 'Персонаж') ?></label>
                        <select name="char_id" class="form-select topup-select" onchange="this.form.submit()">
                            <option value=""><?= Yii::t('views', '— выберите персонажа —') ?></option>
                            <?php foreach ($characters as $c): ?>
                                <?php $cid = (int)($c['char_id'] ?? $c['obj_Id']); ?>
                                <option value="<?= $cid ?>" <?= $char_id === $cid ? 'selected' : '' ?>>
                                    <?= Html::encode($c['char_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if ($char_id && !$ownerOk): ?>
                <div class="alert alert-danger"><?= Yii::t('views', 'Персонаж не принадлежит вам.') ?></div>
            <?php endif; ?>

            <!-- Форма всегда видна, но кнопка блокируется, если персонаж не выбран -->
            <form method="post" action="<?= Url::to(['/cabinet/shop/topup-pay']) ?>" id="buy-form">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                <input type="hidden" name="gs_id" value="<?= $gs_id ?>">
                <input type="hidden" name="char_id" value="<?= $char_id ?>">

                <div class="mb-4">
                    <label class="form-label fw-bold topup-label"><?= Yii::t('views', 'Количество') ?></label>
                    <input type="number" name="qty" id="qty-input" class="form-control form-control-lg" min="1" value="1" required>
                    <small class="text-muted"><?= Yii::t('views', 'Минимум 1.') ?></small>
                </div>

                <!-- Блок калькулятора -->
                <div class="mb-4 p-3 rounded" id="calc-result" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><?= Yii::t('views', 'Базовое количество:') ?></span>
                        <span id="base-qty">1</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success" id="bonus-row" style="display:none;">
                        <span><?= Yii::t('views', 'Бонус:') ?></span>
                        <span id="bonus-qty">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 fw-bold">
                        <span><?= Yii::t('views', 'Итого получите:') ?></span>
                        <span id="total-qty" class="text-warning">1</span>
                    </div>
                    <hr class="my-2" style="border-color: rgba(255,255,255,0.1);">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold"><?= Yii::t('views', 'К оплате:') ?></span>
                        <span class="fs-3 fw-bold text-warning" id="total-price"><?= number_format($price, 2) ?> <?= Html::encode($config->currency) ?></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small topup-label"><?= Yii::t('views', 'Способ оплаты') ?></label>
                    <select name="provider" class="form-select topup-select" required>
                        <?php foreach ($providers as $key => $label): ?>
                            <option value="<?= Html::encode($key) ?>"><?= Html::encode($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($providers)): ?>
                        <div class="text-danger small mt-1"><?= Yii::t('views', 'Приём платежей временно недоступен.') ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-warning w-100 py-2 fw-bold" <?= (!$isReady || empty($providers)) ? 'disabled' : '' ?>>
                    <?= Yii::t('views', 'Пополнить') ?>
                </button>
                <?php if (!$char_id): ?>
                    <div class="text-muted small mt-2 text-center"><?= Yii::t('views', 'Выберите персонажа для покупки.') ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    const tiers = {$tiersJson};
    const basePrice = {$price};
    
    const qtyInput = document.getElementById('qty-input');
    if (qtyInput) {
        qtyInput.addEventListener('input', function() {
            const qty = parseInt(this.value) || 0;
            let bonus = 0;
            
            if (qty > 0) {
                for (let i = 0; i < tiers.length; i++) {
                    const t = tiers[i];
                    if (qty >= t.min && (t.max === 0 || qty <= t.max)) {
                        bonus = t.bonus;
                        break;
                    }
                }
            }
            
            const totalQty = qty + bonus;
            const totalPrice = qty * basePrice;
            
            document.getElementById('base-qty').innerText = qty;
            document.getElementById('total-qty').innerText = totalQty;
            document.getElementById('total-price').innerText = totalPrice.toFixed(2) + ' {$config->currency}';
            
            const bonusRow = document.getElementById('bonus-row');
            if (bonus > 0) {
                document.getElementById('bonus-qty').innerText = '+' + bonus;
                bonusRow.style.display = 'flex';
            } else {
                bonusRow.style.display = 'none';
            }
        });
    }
");
?>