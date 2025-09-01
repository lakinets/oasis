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

/** @var \app\modules\cabinet\models\ShopCategories[] $categories */
/** @var float|int $balance */

$this->title = 'Магазин';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Выберите категорию</h1>

    <!-- Блок «Баланс» справа вверху -->
    <div class="text-end">
        <div class="small text-muted">Баланс</div>
        <div class="fs-5 fw-semibold"><?= (int)$balance ?> Web Aden</div>
    </div>
</div>

<div class="row">
    <?php foreach ($categories as $category): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Html::encode($category->name) ?></h5>
                    <a href="/cabinet/shop/category?category_link=<?= Html::encode($category->link) ?>" class="btn btn-primary">Открыть</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
