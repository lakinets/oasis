<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\modules\backend\models\TransactionsSearch */

$this->title = 'История транзакций';
?>
<div class="transactions-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'id',
            'user_id',
            'aggregator',
            'amount',
            'status',
            'created_at:datetime',
        ],
    ]) ?>
</div>