<div class="card mb-2">
    <div class="card-body">
        <h6>Транзакция #<?= $model->id ?></h6>
        <p>Сумма: <?= $model->sum ?></p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>