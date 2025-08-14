<?php
use yii\helpers\Html;
use yii\widgets\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */
/** @var app\modules\backend\models\LoginServers $ls */

$this->title = Yii::t('backend', 'Логин сервера');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $ls->name . ' - ' . Yii::t('backend', 'аккаунты');
?>
<div class="login-servers-accounts-index">
    <h1><?= Html::encode($ls->name) ?> - <?= Yii::t('backend', 'аккаунты') ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'login',
            [
                'attribute' => 'last_active',
                'format' => 'datetime',
                'value' => fn($row) => $row['last_active'] ?? null,
                'label' => Yii::t('backend', 'Last Active'),
            ],
            [
                'attribute' => 'access_level',
                'label' => Yii::t('backend', 'Access Level'),
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered'],
    ]) ?>
</div>