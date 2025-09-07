<?php
/** @var $model app\modules\install\models\PhpCheckForm */
use yii\helpers\Html;
?>
<div class="site-install-step1">
    <h1>Step 1: Environment check</h1>
    <ul>
        <li>PHP ≥ 8.2: <b class="text-<?= $model->checkPhp()?'success':'danger' ?>">
            <?= $model->checkPhp()?'PASS':'FAIL' ?></b></li>
        <li>PDO MySQL: <b class="text-<?= $model->checkPdo()?'success':'danger' ?>">
            <?= $model->checkPdo()?'PASS':'FAIL' ?></b></li>
        <li>GD extension: <b class="text-<?= $model->checkGd()?'success':'danger' ?>">
            <?= $model->checkGd()?'PASS':'FAIL' ?></b></li>
    </ul>
    <?= Html::a('Next →', ['step2'], ['class' => 'btn btn-primary']) ?>
</div>