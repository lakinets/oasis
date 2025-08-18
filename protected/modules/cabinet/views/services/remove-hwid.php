<?php
use yii\helpers\Html;

$this->title = 'Снять HWID';
?>
<h1>Снять HWID</h1>
<?= Html::beginForm(['/cabinet/services/remove-hwid'], 'post') ?>
<?= Html::submitButton('Снять привязку', ['class' => 'btn btn-danger']) ?>
<?= Html::endForm() ?>