<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><?= \yii\helpers\Html::encode($model->title) ?></h5>
        <p class="card-text"><?= \yii\helpers\Html::encode($model->description) ?></p>
        <a href="/cabinet/shop/buy?pack_id=<?= $model->id ?>" class="btn btn-primary">Купить</a>
    </div>
</div>