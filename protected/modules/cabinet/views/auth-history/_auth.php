<div class="card mb-2">
    <div class="card-body">
        <p><strong>IP:</strong> <?= $model->ip ?></p>
        <p><strong>Браузер:</strong> <?= $model->user_agent ?></p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>