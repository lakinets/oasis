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
/** @var \yii\web\View $this */
/** @var array $providers */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Пополнение баланса';

/* ---------- Текущий баланс ---------- */
$balance = (float)(new \yii\db\Query())
    ->from('user_profiles')
    ->where(['user_id' => Yii::$app->user->id])
    ->select('balance')
    ->scalar();
?>

<!-- Заголовок с балансом справа -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Пополнение баланса</h1>

    <div class="text-end">
        <div class="small text-muted">Баланс</div>
        <div class="fs-5 fw-semibold"><?= (int)$balance ?> Web Aden</div>
    </div>
</div>

<!-- Флеши -->
<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?>"><?= Html::encode($msg) ?></div>
<?php endforeach; ?>

<!-- Форма -->
<form method="post" action="<?= Url::to(['/cabinet/deposit/create']) ?>">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>

    <div style="display:flex; justify-content:center; align-items:flex-start; gap:20px; margin-top:10px; flex-wrap:wrap;">
        <div class="form-group" style="min-width:250px;">
            <label>Укажите на сколько желаете пополнить свой баланс</label>
            <input type="number" name="amount" min="1" step="0.01" class="form-control" required>
        </div>

        <div class="form-group" style="min-width:250px;">
            <label>Платёжная система</label>
            <select name="provider" class="form-control" required>
                <?php foreach ($providers as $key => $label): ?>
                    <option value="<?= Html::encode($key) ?>"><?= Html::encode($label) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($providers)): ?>
                <div class="alert alert-warning text-center" style="margin-top:10px">
                    Приём платежей был отключён администратором.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top:15px; text-align:center;">
        <button class="btn btn-primary" <?= empty($providers) ? 'disabled' : '' ?>>Оплатить</button>
    </div>
</form>

<p style="margin-top:20px;color:#777">
    После оплаты начисление произойдёт автоматически.
</p>
