<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model app\modules\install\models\PhpCheckForm */
?>
<h1>Шаг 1: Проверка хоста и лицензия</h1>

<!-- Лицензия -->
<div class="card mb-4">
    <div class="card-header bg-dark text-white">Лицензионное соглашение Oasis CMS</div>
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

<!-- Чек-бокс согласия -->
<?php $form = ActiveForm::begin(); ?>
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="acceptLicense" required>
        <label class="form-check-label" for="acceptLicense">
            Я принимаю условия соглашения
        </label>
    </div>
<?php ActiveForm::end(); ?>


<h2>Проверка окружения</h2>
<ul>
    <li>PHP ≥ 8.2:
        <b class="text-<?= $model->checkPhp() ? 'success' : 'danger' ?>">
            <?= $model->checkPhp() ? 'Да' : 'Нет' ?>
        </b>
    </li>
    <li>PDO MySQL:
        <b class="text-<?= $model->checkPdo() ? 'success' : 'danger' ?>">
            <?= $model->checkPdo() ? 'Да' : 'Нет' ?>
        </b>
    </li>
    <li>GD extension:
        <b class="text-<?= $model->checkGd() ? 'success' : 'danger' ?>">
            <?= $model->checkGd() ? 'Да' : 'Нет' ?>
        </b>
    </li>
    <li>mod_rewrite:
        <b class="text-<?= $model->checkModRewrite() ? 'success' : 'danger' ?>">
            <?= $model->checkModRewrite() ? 'Да' : 'Нет' ?>
        </b>
    </li>
    <li>Запись в <code>protected/runtime/logs/app.log</code>:
        <b class="text-<?= $model->checkLogWritable() ? 'success' : 'danger' ?>">
            <?= $model->checkLogWritable() ? 'Да' : 'Нет' ?>
        </b>
    </li>
    <li>Запись в <code>online.txt</code>:
        <b class="text-<?= $model->checkOnlineTxtWritable() ? 'success' : 'danger' ?>">
            <?= $model->checkOnlineTxtWritable() ? 'Да' : 'Нет' ?>
        </b>
    </li>
    <li>Запись в <code>robots.txt</code>:
        <b class="text-<?= $model->checkRobotsTxtWritable() ? 'success' : 'danger' ?>">
            <?= $model->checkRobotsTxtWritable() ? 'Да' : 'Нет' ?>
        </b>
    </li>

    <?php if ($model->checkInstallDirWritable() !== null): ?>
        <li>Права на папку <code>protected/modules/install</code> (777):
            <b class="text-<?= $model->checkInstallDirWritable() ? 'success' : 'danger' ?>">
                <?= $model->checkInstallDirWritable() ? 'Да' : 'Нет' ?>
            </b>
        </li>
    <?php endif; ?>
</ul>

<?php
// считаем, сколько тестов «красное»
$failed  = 0;
$failed += $model->checkPhp()            ? 0 : 1;
$failed += $model->checkPdo()           ? 0 : 1;
$failed += $model->checkGd()            ? 0 : 1;
$failed += $model->checkModRewrite()    ? 0 : 1;
$failed += $model->checkLogWritable()   ? 0 : 1;
$failed += $model->checkOnlineTxtWritable() ? 0 : 1;
$failed += $model->checkRobotsTxtWritable() ? 0 : 1;
if ($model->checkInstallDirWritable() !== null) {
    $failed += $model->checkInstallDirWritable() ? 0 : 1;
}
?>

<button id="nextBtn" class="btn btn-primary" disabled>Дальше</button>

<?php if ($failed): ?>
    <p class="alert alert-warning mt-2">
        Некоторые требования не выполнены (<?= $failed ?>). Установка может работать нестабильно.
    </p>
<?php endif; ?>

<script>
    const checkbox = document.getElementById('acceptLicense');
    const nextBtn  = document.getElementById('nextBtn');

    function toggleNext() {
        nextBtn.disabled = !checkbox.checked;
    }
    checkbox.addEventListener('change', toggleNext);
    toggleNext(); // первый запуск

    nextBtn.addEventListener('click', () => {
        if (<?= $failed ?> && !confirm('Продолжить несмотря на предупреждения?')) {
            return;
        }
        window.location.href = '<?= \yii\helpers\Url::to(['step2']) ?>';
    });
</script>
