<?php
use yii\helpers\Html;
?>
<h1><?= Yii::t('install', 'Шаг 3: Установка таблиц') ?></h1>
<pre class="mt-3"><?= $res ?></pre>
<div class="mt-4">
    <?= Html::a(Yii::t('install', 'Дальше'), ['step4'], ['class' => 'btn btn-primary']) ?>
</div>