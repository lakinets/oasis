<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $groups app\modules\backend\models\ConfigGroup[] */

$this->title = 'Настройки';
\yii\bootstrap5\BootstrapAsset::register($this);
?>

<div class="config-page">
    <?php $form = ActiveForm::begin(['id' => 'config-form']); ?>

    <ul class="nav nav-tabs" id="configTab" role="tablist">
        <?php foreach ($groups as $i => $group): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $i === 0 ? 'active' : '' ?>"
                        id="tab-<?= $group->id ?>"
                        data-bs-toggle="tab"
                        data-bs-target="#config-<?= $group->id ?>"
                        type="button" role="tab"
                        aria-controls="config-<?= $group->id ?>"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                    <?= Html::encode(trim($group->name)) ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

<div class="tab-content mt-3 p-3 border border-top-0 rounded-bottom bg-light" style="min-height:2000px;">
    <?php foreach ($groups as $i => $group): ?>
        <div id="config-<?= $group->id ?>"
             class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>"
             role="tabpanel"
             aria-labelledby="tab-<?= $group->id ?>">

            <h5 class="mb-3"><?= Html::encode(trim($group->name)) ?></h5>

            <?php foreach ($group->configs as $config): ?>
                <div class="mb-3">
                    <?= Html::label($config->label, null, ['class' => 'form-label fw-bold']) ?>

                    <?php
                    $fieldName = "Config[{$config->id}][value]";
                    $value     = $config->value;
                    $options   = ['class' => 'form-control'];

                    switch ($config->field_type) {
                        case 'textarea':
                            $options['rows'] = 3;
                            echo Html::textarea($fieldName, $value, $options);
                            break;
                        case 'dropDownList':
                            echo Html::dropDownList($fieldName, $value, $config->getListOptions(), ['class' => 'form-select']);
                            break;
                        case 'passwordField':
                            echo Html::passwordInput($fieldName, $value, $options);
                            break;
                        default:
                            echo Html::textInput($fieldName, $value, $options);
                    }
                    ?>

                    <?php if (!empty($config->description)): ?>
                        <div class="form-text"><?= Html::encode($config->description) ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>
    <?php endforeach; ?>
</div>
