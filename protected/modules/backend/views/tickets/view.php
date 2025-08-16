<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Tickets */

$this->title = Yii::t('backend', 'Тикет #{id}', ['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Тикеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tickets-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Редактировать'), ['edit', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('backend', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => Yii::t('backend', 'Удалить тикет?'),
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <div class="panel panel-default">
        <div class="panel-heading"><?= Yii::t('backend', 'Описание') ?></div>
        <div class="panel-body">
            <?= Yii::$app->formatter->asNtext($model->description) ?>
        </div>
    </div>
</div>