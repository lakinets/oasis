<?php
$this->title = 'Сообщение: ' . $model->title;
?>
<h1><?= Html::encode($model->title) ?></h1>
<div class="card">
    <div class="card-body">
        <p><?= nl2br(Html::encode($model->text)) ?></p>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
    </div>
</div>