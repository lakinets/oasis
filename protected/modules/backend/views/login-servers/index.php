<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use app\modules\backend\models\LoginServers;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\LoginServersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('backend', 'Логин сервера');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-servers-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Создать'), ['form'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['index']),
    ]); ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th><?= Yii::t('backend', 'Название') ?></th>
                    <th width="12%"><?= Yii::t('backend', 'IP') ?></th>
                    <th width="5%"><?= Yii::t('backend', 'Порт') ?></th>
                    <th width="15%"><?= Yii::t('backend', 'Версия') ?></th>
                    <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                    <th width="20%"><?= Yii::t('backend', 'Действия') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->models as $row): ?>
                    <tr>
                        <td><?= $row->id ?></td>
                        <td><?= Html::encode($row->name) ?></td>
                        <td><?= Html::encode($row->ip) ?></td>
                        <td><?= $row->port ?></td>
                        <td><?= Html::encode($row->version) ?></td>
                        <td>
                            <span class="label <?= $row->isStatusOn() ? 'label-success' : 'label-default' ?>">
                                <?= $row->getStatusLabel() ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                <?= Html::a('Изменить', ['/backend/login-servers/form', 'id' => $row->id], ['class' => 'btn btn-primary btn-xs']) ?>
                                <?= Html::a('Удалить', ['/backend/login-servers/del', 'id' => $row->id], ['class' => 'btn btn-danger btn-xs']) ?>
                                <?= Html::a('Вкл/Откл', ['/backend/login-servers/allow', 'id' => $row->id], ['class' => 'btn btn-default btn-xs']) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php ActiveForm::end(); ?>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>