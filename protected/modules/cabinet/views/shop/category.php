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
/**
 * @var \app\modules\cabinet\models\ShopCategories $category
 * @var \app\models\Gs[] $servers
 * @var int|null $gs_id
 * @var \app\modules\cabinet\models\ShopItemsPacks[] $packs
 * @var float|int $balance
 */
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = $category->name;
?>

<h1><?= Html::encode($category->name) ?></h1>

<!-- Flash-сообщения -->
<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?> alert-dismissible fade show" role="alert">
        <?= Html::encode($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endforeach; ?>

<!-- Селектор ТОЛЬКО сервера -->
<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="" class="row g-2 align-items-end">
            <input type="hidden" name="category_link" value="<?= Html::encode($category->link) ?>">

            <div class="col-md-4">
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

            <div class="col-md-4">
                <div class="small text-muted">Баланс</div>
                <div class="fs-5 fw-semibold"><?= (int)$balance ?> Web Aden</div>
            </div>
        </form>
    </div>
</div>

<?php if (!$gs_id): ?>
    <div class="alert alert-info">Выберите сервер, чтобы увидеть товары.</div>
<?php elseif (empty($packs)): ?>
    <div class="alert alert-info">В категории пока нет товаров.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($packs as $pack): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= Html::encode($pack->title) ?></h5>
                        <?php if ($pack->description): ?>
                            <p class="card-text small text-muted"><?= nl2br(Html::encode($pack->description)) ?></p>
                        <?php endif; ?>
                        <a class="btn btn-primary btn-sm"
                           href="<?= Url::to(['/cabinet/shop/pack',
                               'category_link' => $category->link,
                               'pack_id'       => $pack->id,
                               'gs_id'         => $gs_id       // <-- только сервер
                           ]) ?>">
                            Посмотреть товары
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>