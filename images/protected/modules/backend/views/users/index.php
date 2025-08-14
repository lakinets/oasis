<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\backend\models\Users;

$this->title = 'Пользователи';
?>
<p><?= Html::a('Добавить', ['form'], ['class' => 'btn btn-success']) ?></p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        'user_id',
        'login',
        'email',
        [
            'attribute' => 'role',
            'value'     => fn($m) => $m->getRoleLabel(),
            'filter'    => [Users::ROLE_ADMIN => 'Админ', Users::ROLE_DEFAULT => 'Пользователь'],
        ],
        [
            'attribute' => 'activated',
            'format'    => 'boolean',
            'filter'    => [1 => 'Да', 0 => 'Нет'],
        ],
        'created_at:datetime',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => fn($url, $model) => Html::a('Подробно', ['view', 'user_id' => $model->user_id], ['class' => 'btn btn-xs btn-primary']),
            ],
        ],
    ],
]) ?>
