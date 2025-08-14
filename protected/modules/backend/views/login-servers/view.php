<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить эту страницу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div>
        <?= $model->content ?>
    </div>

</div>
