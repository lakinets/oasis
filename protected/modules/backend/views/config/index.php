<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $groups app\modules\backend\models\ConfigGroup[] */
?>

<h1><?= Yii::t('backend', 'Настройки') ?></h1>

<?php $form = ActiveForm::begin(['id' => 'config-form']); ?>

<!-- 1. просто перечисляем все параметры подряд -->
<?php foreach ($groups as $group): ?>
    <h4><?= Html::encode($group->name) ?></h4>

    <?php foreach ($group->configs as $config): ?>
        <div class="mb-3">
            <label class="form-label fw-bold"><?= Html::encode($config->label) ?></label>
            <?= $config->getField() ?>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

<div class="mt-3">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>