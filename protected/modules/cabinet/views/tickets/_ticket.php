<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">
            <?= \yii\helpers\Html::a($model->title, ['/cabinet/tickets/view', 'ticket_id' => $model->id]) ?>
        </h5>
        <p class="card-text">
            Статус: <span class="badge badge-<?= $model->status ? 'success' : 'secondary' ?>">
                <?= $model->status ? 'Открыт' : 'Закрыт' ?>
            </span>
        </p>
        <small class="text-muted">Создано: <?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>