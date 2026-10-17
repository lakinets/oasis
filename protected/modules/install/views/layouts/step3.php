<?php
use yii\helpers\Html;
?>
<h1><?= Yii::t('install', 'Шаг 3: Выполнение миграций') ?></h1>
<div class="alert alert-info mt-3">
    <pre class="mb-0 bg-transparent border-0 text-dark"><?= $res ?></pre>
</div>
<div class="mt-4">
    <?= Html::a(Yii::t('install', 'Далее →'), ['step4'], ['class' => 'btn btn-primary']) ?>
</div>