<div class="card mb-2">
    <div class="card-body">
        <p>+<?= $model->profit ?> от реферала</p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>