<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var array $gateway */
/** @var string $code */

$this->title = Yii::t('backend', 'Настройка: {name}', ['name' => Yii::t('backend', $gateway['name'])]);
$configs = $gateway['configs'] ?? [];
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('← ' . Yii::t('backend', 'К списку шлюзов'), ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
</p>

<?php if (empty($configs)): ?>
    <div class="alert alert-warning">
        <?= Yii::t('backend', 'Для этого шлюза нет записей в таблице config. Добавьте их SQL-запросом или через установщик.') ?>
    </div>
<?php else: ?>
    <?php $form = ActiveForm::begin(['id' => 'payments-form-' . $code]); ?>

    <?php foreach ($configs as $config): ?>
        <div class="mb-3">
            <label class="form-label fw-bold">
                <?= Html::encode(Yii::t('backend', $config->label)) ?>
                <small class="text-muted">(<?= Html::encode($config->param) ?>)</small>
            </label>
            <?= $config->getField() ?>
        </div>
    <?php endforeach; ?>

    <div class="mt-3">
        <?= Html::submitButton(Yii::t('backend', 'Сохранить'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
<?php endif; ?>