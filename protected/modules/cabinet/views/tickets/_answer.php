<?php
use yii\helpers\Html;
?>

<div class="card mb-2">
    <div class="card-body">
        <p class="mb-1"><?= nl2br(Html::encode($model->text)) ?></p>
        <small class="text-muted">
            <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
        </small>
    </div>
</div>
