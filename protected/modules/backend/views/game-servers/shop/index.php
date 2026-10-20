<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\backend\models\Gs;

/* @var $gs app\modules\backend\models\Gs */
/* @var $categories app\modules\backend\models\ShopCategories[] */
/* @var $topupConfig app\modules\backend\models\ShopTopupConfig */
/* @var $mode string */

$mode = $mode ?? 'shop';
$modes = Gs::getShopModeList();
?>
<h1><?= Yii::t('backend', 'Магазин сервера') ?> <?= Html::encode($gs->name) ?></h1>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?> alert-dismissible fade show" role="alert">
        <?= Html::encode($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="post"
              action="<?= Url::to(['/backend/game-servers/shop-mode-toggle', 'gs_id' => $gs->id]) ?>"
              class="row g-2 align-items-end">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <div class="col-md-6">
                <label class="form-label fw-bold"><?= Yii::t('backend', 'Режим магазина') ?></label>
                <select name="mode" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($modes as $key => $label): ?>
                        <option value="<?= Html::encode($key) ?>" <?= $mode === $key ? 'selected' : '' ?>>
                            <?= Html::encode($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <noscript>
                    <button type="submit" class="btn btn-primary"><?= Yii::t('backend', 'Применить') ?></button>
                </noscript>
            </div>
        </form>
    </div>
</div>

<?php if ($mode === 'shop'): ?>
    <!-- ============ СТАНДАРТНЫЙ РЕЖИМ ============ -->
    <div class="d-flex mb-3">
        <?= Html::a(
            Yii::t('backend', 'Создать категорию'),
            ['/backend/game-servers/shop-category-form', 'gs_id' => $gs->id],
            ['class' => 'btn btn-success mr-2']
        ) ?>
    </div>

    <div class="list-group">
        <?php foreach ($categories as $category): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <?= Html::a(
                        Html::encode($category->getName()),
                        ['/backend/game-servers/shop-category', 'gs_id' => $gs->id, 'category_id' => $category->id],
                        ['class' => 'font-weight-bold']
                    ) ?>
                </div>

                <div class="btn-group btn-group-sm" role="group">
                    <?= Html::a(
                        Yii::t('backend', 'Редактировать'),
                        ['/backend/game-servers/shop-category-form', 'gs_id' => $gs->id, 'category_id' => $category->id],
                        ['class' => 'btn btn-warning', 'style' => 'font-size: 11px; padding: 2px 6px;']
                    ) ?>
                    <?= Html::a(
                        Yii::t('backend', 'Удалить'),
                        ['/backend/game-servers/shop-category-del', 'category_id' => $category->id],
                        [
                            'class' => 'btn btn-danger',
                            'style' => 'font-size: 11px; padding: 2px 6px;',
                            'data' => ['confirm' => Yii::t('backend', 'Удалить категорию?'), 'method' => 'post'],
                        ]
                    ) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
    <!-- ============ РЕЖИМ "ПОПОЛНЕНИЕ" ============ -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?= Yii::t('backend', 'Конфигурация пополнения') ?></h5>
            <?= Html::a(
                Yii::t('backend', 'Настроить'),
                ['/backend/game-servers/shop-topup', 'gs_id' => $gs->id],
                ['class' => 'btn btn-primary btn-sm']
            ) ?>
        </div>
        <div class="card-body">
            <?php if ((int)$topupConfig->id > 0): ?>
                <dl class="row mb-0">
                    <dt class="col-sm-3"><?= Yii::t('backend', 'ID предмета') ?></dt>
                    <dd class="col-sm-9"><?= (int)$topupConfig->item_id ?></dd>

                    <dt class="col-sm-3"><?= Yii::t('backend', 'Название') ?></dt>
                    <dd class="col-sm-9"><?= Html::encode($topupConfig->item_name) ?></dd>

                    <dt class="col-sm-3"><?= Yii::t('backend', 'Количество') ?></dt>
                    <dd class="col-sm-9"><?= (int)$topupConfig->item_count ?></dd>

                    <dt class="col-sm-3"><?= Yii::t('backend', 'Цена') ?></dt>
                    <dd class="col-sm-9"><?= number_format((float)$topupConfig->price, 2, '.', ' ') ?> <?= Html::encode($topupConfig->currency) ?></dd>

                    <dt class="col-sm-3"><?= Yii::t('backend', 'Порогов бонусов') ?></dt>
                    <dd class="col-sm-9"><?= count($topupConfig->tiers) ?></dd>

                    <dt class="col-sm-3"><?= Yii::t('backend', 'Статус') ?></dt>
                    <dd class="col-sm-9 mb-0">
                        <?= $topupConfig->status ? Yii::t('backend', 'Активен') : Yii::t('backend', 'Неактивен') ?>
                    </dd>
                </dl>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <?= Yii::t('backend', 'Конфигурация ещё не создана.') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>