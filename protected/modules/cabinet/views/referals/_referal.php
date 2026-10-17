<div class="card mb-2">
    <div class="card-body">
        <p><?= Yii::t('views', '+{profit} от реферала', ['profit' => $model->profit]) ?></p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>