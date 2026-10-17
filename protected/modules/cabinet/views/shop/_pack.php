<?php
/** @var \app\modules\cabinet\models\ShopItemsPacks $model */
$packDescr = $model->getDescription();
?>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><?= \yii\helpers\Html::encode($model->getTitle()) ?></h5>
        <?php if ($packDescr !== ''): ?>
            <p class="card-text"><?= \yii\helpers\Html::encode($packDescr) ?></p>
        <?php endif; ?>
        <a href="/cabinet/shop/buy?pack_id=<?= $model->id ?>" class="btn btn-primary"><?= Yii::t('views', 'Купить') ?></a>
    </div>
</div>