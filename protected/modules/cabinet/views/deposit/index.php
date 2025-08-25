<?php
/** @var \yii\web\View $this */
/** @var array $providers */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Пополнение баланса';
?>
<h1>Пополнение баланса</h1>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= Html::encode($type) ?>"><?= Html::encode($msg) ?></div>
<?php endforeach; ?>

<form method="post" action="<?= Url::to(['/cabinet/deposit/create']) ?>">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>

    <div class="form-group">
        <label>Сумма (RUB)</label>
        <input type="number" name="amount" min="1" step="0.01" class="form-control" required>
    </div>

    <div class="form-group" style="margin-top:10px">
        <label>Платёжная система</label>
        <select name="provider" class="form-control" required>
            <?php foreach ($providers as $key => $label): ?>
                <option value="<?= Html::encode($key) ?>"><?= Html::encode($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (empty($providers)): ?>
            <div class="alert alert-warning" style="margin-top:10px">
                Нет доступных платёжных систем. Заполните настройки в админке.
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-top:15px">
        <button class="btn btn-primary" <?= empty($providers) ? 'disabled' : '' ?>>Оплатить</button>
    </div>
</form>

<p style="margin-top:20px;color:#777">
    После оплаты начисление произойдёт автоматически. История фиксируется в `payment_transactions`.
</p>
