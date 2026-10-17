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
<?php
use yii\helpers\Html;

/** @var \app\modules\cabinet\models\ShopCategories[] $categories */
/** @var float|int $balance */

$this->title = Yii::t('views', 'Магазин');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= Yii::t('views', 'Выберите категорию') ?></h1>

    <div class="text-end">
        <div class="small text-muted"><?= Yii::t('views', 'Баланс') ?></div>
        <div class="fs-5 fw-semibold"><?= (int)$balance ?> Web Aden</div>
    </div>
</div>

<div class="row">
    <?php foreach ($categories as $category): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode($category->getName()) ?></h5>
                    <a href="/cabinet/shop/category?category_link=<?= Html::encode($category->link) ?>" class="btn btn-primary"><?= Yii::t('views', 'Открыть') ?></a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>