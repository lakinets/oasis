<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\backend\models\Bonuses;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\backend\models\BonusCodesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $bonus_id int|null */

$this->title = 'Коды бонусов';
$this->params['breadcrumbs'][] = ['label' => 'Бонусы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonus-codes-index">

    <p>
        <?= Html::a('Создать код', ['code-form', 'bonus_id' => $bonus_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Назад к бонусам', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            'id',
            'code',
            [
                'attribute' => 'bonus_id',
                'value'     => function ($m) {
                    return $m->bonus ? $m->bonus->title : '-';
                },
                'filter'    => ArrayHelper::map(Bonuses::find()->all(), 'id', 'title'),
            ],
            'limit',
            [
                'attribute' => 'status',
                'value'     => function ($m) {
                    return $m->getStatusName();
                },
                'filter'    => [1 => 'Активирован', 0 => 'Выключен'],
            ],

            /* ------------- ССЫЛКИ ДЕЙСТВИЙ ------------- */
            [
                'class'    => \yii\grid\ActionColumn::class,
                'template' => '{update} {toggle} {delete}',
                'buttons'  => [
                    'update' => function ($url, $model) use ($bonus_id) {
                        return Html::a(
                            'Редактировать',
                            ['code-form', 'id' => $model->id, 'bonus_id' => $bonus_id]
                        );
                    },
                    'toggle' => function ($url, $model) use ($bonus_id) {
                        return Html::a(
                            $model->status ? 'Деактивировать' : 'Активировать',
                            ['code-toggle', 'id' => $model->id, 'bonus_id' => $bonus_id],
                            [
                                'data-confirm' => 'Изменить статус кода?',
                                'data-method'  => 'post',
                            ]
                        );
                    },
                    'delete' => function ($url, $model) use ($bonus_id) {
                        return Html::a(
                            'Удалить',
                            ['code-delete', 'id' => $model->id, 'bonus_id' => $bonus_id],
                            [
                                'data-confirm' => 'Удалить этот код?',
                                'data-method'  => 'post',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>