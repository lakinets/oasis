<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Бонусы');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonuses-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Создать бонус'), ['form'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'title',
            'date_end',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusName();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {toggle} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('backend', 'Редактировать'), ['form', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
                    },
                    'toggle' => function ($url, $model) {
                        $label = $model->status == $model::STATUS_ACTIVE ? Yii::t('backend', 'Деактивировать') : Yii::t('backend', 'Активировать');
                        return Html::a($label, ['allow', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('backend', 'Удалить'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => Yii::t('backend', 'Точно удалить бонус?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>

