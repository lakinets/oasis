<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\modules\backend\models\Gallery $model */
?>
<div class="item">
    <img src="<?= $model->imageUrl ?>" width="150">
    <p><?= Html::encode($model->title) ?></p>
    <p>
        <?= Html::a(Yii::t('backend', 'Редактировать'), ['form', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']) ?>
        <?= Html::a(Yii::t('backend', 'Удалить'), ['del', 'id' => $model->id], [
            'class' => 'btn btn-xs btn-danger',
            'data'  => ['confirm' => Yii::t('backend', 'Удалить?'), 'method' => 'post'],
        ]) ?>
    </p>
</div>
