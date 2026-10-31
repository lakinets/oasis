<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var array $gateways */

$this->title = Yii::t('backend', 'Платёжные системы');
?>

<h1><?= Html::encode($this->title) ?></h1>

<p class="text-muted">
    <?= Yii::t('backend', 'Управление шлюзами, ключами и тестовым режимом. Значения хранятся в таблице config.') ?>
</p>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>
<?php if (Yii::$app->session->hasFlash('info')): ?>
    <div class="alert alert-info"><?= Yii::$app->session->getFlash('info') ?></div>
<?php endif; ?>

<div class="row">
<?php foreach ($gateways as $gw): ?>
    <?php
        $cardClass = 'border-secondary';
        if ($gw['is_active']) {
            $cardClass = $gw['is_test'] ? 'border-warning' : 'border-success';
        }
    ?>
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card h-100 <?= $cardClass ?>">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= Html::encode(Yii::t('backend', $gw['name'])) ?></strong>
                    <div class="small text-muted">
                        <?= Html::encode($gw['region'] === 'cis' ? Yii::t('backend', 'СНГ') : Yii::t('backend', 'Глобальный')) ?>
                    </div>
                </div>
                <div>
                    <?php if ($gw['is_active']): ?>
                        <?php if ($gw['supports_test'] && $gw['is_test']): ?>
                            <span class="badge bg-warning text-dark">TEST</span>
                        <?php elseif ($gw['is_active']): ?>
                            <span class="badge bg-success">LIVE</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="badge bg-secondary">OFF</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text"><?= Html::encode(Yii::t('backend', $gw['description'])) ?></p>

                <p class="mb-1">
                    <span class="text-muted"><?= Yii::t('backend', 'Статус ключей:') ?></span>
                    <?php if ($gw['is_configured']): ?>
                        <span class="badge bg-info text-dark"><?= Yii::t('backend', 'заполнены') ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger"><?= Yii::t('backend', 'не заполнены') ?></span>
                    <?php endif; ?>
                </p>

                <p class="mb-0">
                    <span class="text-muted"><?= Yii::t('backend', 'Тестовый режим:') ?></span>
                    <?php if ($gw['supports_test']): ?>
                        <?= $gw['is_test']
                            ? '<span class="badge bg-warning text-dark">' . Yii::t('backend', 'вкл') . '</span>'
                            : '<span class="badge bg-secondary">' . Yii::t('backend', 'выкл') . '</span>' ?>
                    <?php elseif ($gw['has_test']): ?>
                        <span class="badge bg-light text-dark"><?= Yii::t('backend', 'сервис поддерживает, код — в Фазе 2') ?></span>
                    <?php else: ?>
                        <span class="badge bg-light text-dark"><?= Yii::t('backend', 'не поддерживается') ?></span>
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Кнопки действий переведены на POST-формы для защиты от CSRF -->
            <div class="card-footer d-flex flex-wrap gap-1">
                
                <?= Html::beginForm(['form', 'code' => $gw['code']], 'get', ['class' => 'flex-fill d-flex']) ?>
                    <?= Html::submitButton(Yii::t('backend', 'Настроить'), ['class' => 'btn btn-sm btn-primary w-100']) ?>
                <?= Html::endForm() ?>

                <?= Html::beginForm(['ping', 'code' => $gw['code']], 'post', ['class' => 'flex-fill d-flex']) ?>
                    <?= Html::submitButton(Yii::t('backend', 'Проверить'), ['class' => 'btn btn-sm btn-outline-info w-100']) ?>
                <?= Html::endForm() ?>

                <?= Html::beginForm(['toggle', 'code' => $gw['code']], 'post', ['class' => 'flex-fill d-flex']) ?>
                    <?= Html::submitButton(
                        $gw['is_active'] ? Yii::t('backend', 'Выключить') : Yii::t('backend', 'Включить'),
                        ['class' => 'btn btn-sm ' . ($gw['is_active'] ? 'btn-outline-danger' : 'btn-outline-success') . ' w-100']
                    ) ?>
                <?= Html::endForm() ?>

                <?php if ($gw['supports_test']): ?>
                    <?= Html::beginForm(['toggle-test', 'code' => $gw['code']], 'post', ['class' => 'flex-fill d-flex']) ?>
                        <?= Html::submitButton(
                            $gw['is_test'] ? Yii::t('backend', 'Откл. тест') : Yii::t('backend', 'Вкл. тест'),
                            ['class' => 'btn btn-sm ' . ($gw['is_test'] ? 'btn-outline-secondary' : 'btn-outline-warning') . ' w-100']
                        ) ?>
                    <?= Html::endForm() ?>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php if (empty($gateways)): ?>
    <div class="alert alert-warning">
        <?= Yii::t('backend', 'Список шлюзов пуст. Проверьте config/payment_gateways.php.') ?>
    </div>
<?php endif; ?>