<?php
/**
 * @var \app\models\Gs[] $servers
 * @var int|null $gs_id
 * @var array $characters  // [['char_id'=>..., 'char_name'=>...], ...]
 * @var int|null $char_id
 * @var string $category_link
 * @var float|int $balance
 */
use yii\helpers\Html;
use yii\helpers\Url;

$actionUrl = Url::to(['/cabinet/shop/category', 'category_link' => $category_link]);
?>
<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="<?= Html::encode($actionUrl) ?>" class="row g-2 align-items-end">
            <input type="hidden" name="category_link" value="<?= Html::encode($category_link) ?>">

            <div class="col-md-3">
                <label class="form-label">Сервер</label>
                <select name="gs_id" class="form-select" onchange="this.form.submit()">
                    <option value="">— выберите сервер —</option>
                    <?php foreach ($servers as $s): ?>
                        <option value="<?= (int)$s->id ?>" <?= (int)$gs_id === (int)$s->id ? 'selected' : '' ?>>
                            <?= Html::encode($s->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Персонаж</label>
                <select name="char_id" class="form-select" <?= $gs_id ? '' : 'disabled' ?> onchange="this.form.submit()">
                    <option value="">— выберите персонажа —</option>
                    <?php foreach ($characters as $ch): ?>
                        <option value="<?= (int)$ch['char_id'] ?>" <?= (int)($char_id ?? 0) === (int)$ch['char_id'] ? 'selected' : '' ?>>
                            <?= Html::encode($ch['char_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
				<div class="small text-muted">Баланс</div>
				<div class="fs-5 fw-semibold">
					<?= (int)$balance ?> Web Aden
				</div>
			</div>

            <div class="col-md-3 text-end">
                <?php if (!$gs_id): ?>
                    <div class="text-danger">Выберите сервер</div>
                <?php elseif (!$char_id): ?>
                    <div class="text-warning">Выберите персонажа</div>
                <?php else: ?>
                    <div class="text-success">Готово к покупке</div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
