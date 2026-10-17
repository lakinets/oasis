<div class="card mb-2">
    <div class="card-body">
        <h6><?= Yii::t('views', 'Транзакция #{id}', ['id' => $model->id]) ?></h6>
        <p><?= Yii::t('views', 'Сумма: {sum}', ['sum' => $model->sum]) ?></p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>