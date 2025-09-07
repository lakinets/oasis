<?php
use yii\helpers\Html;
/** @var $model app\modules\install\models\PhpCheckForm */
?>
<h1>Шаг 1: Проверка хоста</h1>

<ul>
    <li>PHP ≥ 8.2:
        <b class="text-<?= $model->checkPhp()?'success':'danger' ?>">
            <?= $model->checkPhp()?'Да':'Нет' ?>
        </b>
    </li>
    <li>PDO MySQL:
        <b class="text-<?= $model->checkPdo()?'success':'danger' ?>">
            <?= $model->checkPdo()?'Да':'Нет' ?>
        </b>
    </li>
    <li>GD extension:
        <b class="text-<?= $model->checkGd()?'success':'danger' ?>">
            <?= $model->checkGd()?'Да':'Нет' ?>
        </b>
    </li>
    <li>mod_rewrite:
        <b class="text-<?= $model->checkModRewrite()?'success':'danger' ?>">
            <?= $model->checkModRewrite()?'Да':'Нет' ?>
        </b>
    </li>
    <li>Запись в <code>protected/runtime/logs/app.log</code>:
        <b class="text-<?= $model->checkLogWritable()?'success':'danger' ?>">
            <?= $model->checkLogWritable()?'Да':'Нет' ?>
        </b>
    </li>
    <li>Запись в <code>online.txt</code>:
        <b class="text-<?= $model->checkOnlineTxtWritable()?'success':'danger' ?>">
            <?= $model->checkOnlineTxtWritable()?'Да':'Нет' ?>
        </b>
    </li>
    <li>Запись в <code>robots.txt</code>:
        <b class="text-<?= $model->checkRobotsTxtWritable()?'success':'danger' ?>">
            <?= $model->checkRobotsTxtWritable()?'Да':'Нет' ?>
        </b>
    </li>

    <?php if ($model->checkInstallDirWritable() !== null): ?>
        <li>Права на папку <code>protected/modules/install</code> (777):
            <b class="text-<?= $model->checkInstallDirWritable()?'success':'danger' ?>">
                <?= $model->checkInstallDirWritable()?'Да':'Нет' ?>
            </b>
        </li>
    <?php endif; ?>
</ul>

<?php
$allChecks = $model->checkPhp()
    && $model->checkPdo()
    && $model->checkGd()
    && $model->checkModRewrite()
    && $model->checkLogWritable()
    && $model->checkOnlineTxtWritable()
    && $model->checkRobotsTxtWritable();

if ($model->checkInstallDirWritable() !== null) {
    $allChecks = $allChecks && $model->checkInstallDirWritable();
}
?>

<?php if ($allChecks): ?>
    <?= Html::a('Дальше', ['step2'], ['class' => 'btn btn-primary']) ?>
<?php else: ?>
    <p class="alert alert-danger">
        Исправьте ошибки выше, чтобы продолжить установку.
    </p>
<?php endif; ?>
