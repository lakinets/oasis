<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\backend\models\ShopTopupConfig;
use app\modules\backend\models\ShopTopupTier;

/* @var $gs app\modules\backend\models\Gs */
/* @var $config app\modules\backend\models\ShopTopupConfig */

$tiers = $config->isNewRecord ? [] : $config->tiers;
$tiersArr = [];
foreach ($tiers as $t) {
    $tiersArr[] = $t;
}
// Если порогов нет — даём 3 пустые строки
if (empty($tiersArr)) {
    for ($i = 0; $i < 3; $i++) {
        $tiersArr[] = new ShopTopupTier(['min_qty' => '', 'max_qty' => 0, 'bonus_qty' => '']);
    }
}

// Считаем следующий индекс для JS и переводим метки заранее
$nextIndex  = count($tiersArr);
$labelFrom  = Yii::t('backend', 'От (кол-во)');
$labelTo    = Yii::t('backend', 'До (0 = ∞)');
$labelBonus = Yii::t('backend', 'Бонус (кол-во)');
$labelDel   = Yii::t('backend', 'Удалить');
?>
<h1><?= Yii::t('backend', 'Настройка пополнения') ?> — <?= Html::encode($gs->name) ?></h1>

<?= Html::a(
    '← ' . Yii::t('backend', 'Назад к магазину'),
    ['shop', 'gs_id' => $gs->id],
    ['class' => 'btn btn-secondary btn-sm mb-3']
) ?>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?> alert-dismissible fade show" role="alert">
        <?= Html::encode($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>

<?php $form = ActiveForm::begin([
    'id'     => 'topup-form',
    'action' => ['shop-topup', 'gs_id' => $gs->id],
]); ?>

<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0"><?= Yii::t('backend', 'Основной предмет') ?></h5></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <?= $form->field($config, 'item_id')->textInput(['type' => 'number', 'min' => 1]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($config, 'item_count')->textInput(['type' => 'number', 'min' => 1]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($config, 'status')->dropDownList([
                    1 => Yii::t('backend', 'Активен'),
                    0 => Yii::t('backend', 'Неактивен'),
                ]) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($config, 'item_name')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($config, 'description_ru')->textarea(['rows' => 4]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($config, 'description_en')->textarea(['rows' => 4]) ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($config, 'price')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($config, 'currency')->dropDownList(ShopTopupConfig::getCurrencyList()) ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?= Yii::t('backend', 'Пороги бонусов') ?></h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-tier">
            <i class="fas fa-plus"></i> <?= Yii::t('backend', 'Добавить порог') ?>
        </button>
    </div>
    <div class="card-body">
        <p class="text-muted small">
            <?= Yii::t('backend', 'Если игрок покупает количество в указанном диапазоне, ему начисляется бонус. "До" = 0 означает без ограничений. Если таблица пуста — продажа без бонусов.') ?>
        </p>
        <div id="tiers-container">
            <?php foreach ($tiersArr as $i => $t): ?>
            <div class="row g-2 mb-2 tier-row align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted"><?= $labelFrom ?></label>
                    <input type="number" class="form-control" name="tiers[<?= $i ?>][min_qty]" min="1" value="<?= Html::encode($t->min_qty) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted"><?= $labelTo ?></label>
                    <input type="number" class="form-control" name="tiers[<?= $i ?>][max_qty]" min="0" value="<?= Html::encode($t->max_qty) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted"><?= $labelBonus ?></label>
                    <input type="number" class="form-control" name="tiers[<?= $i ?>][bonus_qty]" min="1" value="<?= Html::encode($t->bonus_qty) ?>">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger w-100 remove-tier" style="display:<?= $i < 3 ? 'none' : 'inline-block' ?>"><?= $labelDel ?></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="mb-3">
    <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
let tierIndex = {$nextIndex};

document.getElementById('add-tier').addEventListener('click', function() {
    const container = document.getElementById('tiers-container');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 tier-row align-items-end';
    row.innerHTML = `
        <div class="col-md-3">
            <label class="form-label small text-muted">{$labelFrom}</label>
            <input type="number" class="form-control" name="tiers[\${tierIndex}][min_qty]" min="1">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">{$labelTo}</label>
            <input type="number" class="form-control" name="tiers[\${tierIndex}][max_qty]" min="0" value="0">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">{$labelBonus}</label>
            <input type="number" class="form-control" name="tiers[\${tierIndex}][bonus_qty]" min="1">
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger w-100 remove-tier">{$labelDel}</button>
        </div>
    `;
    container.appendChild(row);
    tierIndex++;
});

document.getElementById('tiers-container').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-tier')) {
        e.target.closest('.tier-row').remove();
    }
});
JS);
?>