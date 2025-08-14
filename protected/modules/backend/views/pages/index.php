<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\backend\models\Pages;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страницы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить страницу', ['form'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // фильтров сверху нет — не передаём filterModel
        'columns' => [
            'id',
            [
                'attribute' => 'title',
                'format' => 'text',
                // показываем просто текст — без ссылки
                'value' => function (Pages $model) {
                    return $model->title;
                },
            ],
            [
                'attribute' => 'page',
                'label' => 'Имя страницы (page)',
                'format' => 'text',
                'value' => function (Pages $model) {
                    return $model->page;
                },
            ],
            [
                'attribute' => 'status',
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function (Pages $model) {
                    return $model->status ? 'Активна' : 'Не активна';
                },
                'filter' => false,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    // Просмотр на фронтенде (используем колонку page)
                    'view' => function ($url, $model, $key) {
                        $host = Yii::$app->request->hostInfo;
                        $page = $model->page ?: $model->id;
                        $urlFrontend = rtrim($host, '/') . '/page/' . $page;
                        return Html::a('Просмотр', $urlFrontend, [
                            'class' => 'btn btn-xs btn-default',
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                        ]);
                    },
                    // Редактировать — ведёт на actionForm (редактирование в бэкенде)
                    'update' => function ($url, $model, $key) {
                        return Html::a('Редактировать', ['form', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-primary'
                        ]);
                    },
                    // Удаление (soft delete) — ведёт на actionDel
                    'delete' => function ($url, $model, $key) {
                        return Html::a('Удалить', ['del', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-danger',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить эту страницу?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
