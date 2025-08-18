<div class="card mb-2">
    <div class="card-body">
        <h6><?= Html::a(Html::encode($model->title), ['/cabinet/messages/detail', 'id' => $model->id]) ?></h6>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>