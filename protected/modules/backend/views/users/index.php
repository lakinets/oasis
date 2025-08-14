<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\backend\models\Users;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // ❗ убираем filterModel, чтобы не было пустых фильтров
        // 'filterModel'  => null,
        'columns' => [
            'user_id',
            [
                'attribute' => 'login',
                'format'    => 'raw',
                'value'     => fn(Users $model) =>
                    Html::a(
                        Html::encode($model->login),
                        ['view', 'user_id' => $model->user_id]
                    ),
            ],
            'email',
            'role',
            [
                'attribute' => 'activated',
                'format'    => 'boolean',
                'value'     => fn($m) => $m->activated ? 'Да' : 'Нет',
            ],
        ],
    ]); ?>
</div>