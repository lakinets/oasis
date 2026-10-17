<?php
use yii\helpers\Html;
/** @var $model app\modules\install\models\PhpCheckForm */
/** @var $failed int */

foreach (Yii::$app->session->getAllFlashes() as $type => $msg) {
    echo '<div class="alert alert-danger mt-3">' . $msg . '</div>';
}
?>
<h1><?= Yii::t('install', 'Шаг 1: Проверка хоста и лицензия') ?></h1>

<form id="installForm" method="post" action="">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

    <!-- Лицензия -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white"><?= Yii::t('install', 'Лицензионное соглашение Oasis CMS') ?></div>
        <div class="card-body" style="max-height: 250px; overflow-y: auto; background:#f8f9fa; white-space: pre-line; font-size:14px;">
Copyright (c) 2025 lakinets

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
</div>

    <!-- Чек-бокс согласия с лицензией -->
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="acceptLicense" name="accept_license" value="1">
        <label class="form-check-label" for="acceptLicense">
            <?= Yii::t('install', 'Я принимаю условия соглашения') ?>
        </label>
    </div>

    <h2><?= Yii::t('install', 'Проверка окружения') ?></h2>
    <ul>
        <li>PHP ≥ 8.2:
            <b class="text-<?= $model->checkPhp() ? 'success' : 'danger' ?>">
                <?= $model->checkPhp() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>
        <li>PDO MySQL:
            <b class="text-<?= $model->checkPdo() ? 'success' : 'danger' ?>">
                <?= $model->checkPdo() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>
        <li>GD extension:
            <b class="text-<?= $model->checkGd() ? 'success' : 'danger' ?>">
                <?= $model->checkGd() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>
        <li>mod_rewrite:
            <b class="text-<?= $model->checkModRewrite() ? 'success' : 'danger' ?>">
                <?= $model->checkModRewrite() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>
        <li><?= Yii::t('install', 'Запись в') ?> <code>protected/runtime/logs/app.log</code>:
            <b class="text-<?= $model->checkLogWritable() ? 'success' : 'danger' ?>">
                <?= $model->checkLogWritable() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>
        <li><?= Yii::t('install', 'Запись в') ?> <code>online.txt</code>:
            <b class="text-<?= $model->checkOnlineTxtWritable() ? 'success' : 'danger' ?>">
                <?= $model->checkOnlineTxtWritable() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>
        <li><?= Yii::t('install', 'Запись в') ?> <code>robots.txt</code>:
            <b class="text-<?= $model->checkRobotsTxtWritable() ? 'success' : 'danger' ?>">
                <?= $model->checkRobotsTxtWritable() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
            </b>
        </li>

        <?php if ($model->checkInstallDirWritable() !== null): ?>
            <li><?= Yii::t('install', 'Права на папку') ?> <code>protected/modules/install</code> (777):
                <b class="text-<?= $model->checkInstallDirWritable() ? 'success' : 'danger' ?>">
                    <?= $model->checkInstallDirWritable() ? Yii::t('install', 'Да') : Yii::t('install', 'Нет') ?>
                </b>
            </li>
        <?php endif; ?>
    </ul>

    <?php if ($failed): ?>
        <div class="alert alert-warning mt-3">
            <p class="mb-0">
                <b><?= Yii::t('install', 'У вас остались замечания, но вы можете продолжить установку. Нажимая далее вы соглашаетесь с тем что стабильная работа сайта не будет гарантирована и до устранения этих замечаний после установки сайта.') ?></b>
            </p>
        </div>
    <?php endif; ?>

    <!-- Кнопка всегда активна, никаких скриптов -->
    <button id="nextBtn" type="submit" class="btn btn-primary mt-3"><?= Yii::t('install', 'Дальше') ?></button>
</form>