<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
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
        <?= Html::a(
            '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('backend', 'Создать'),
            ['form'],
            ['class' => 'btn btn-success']
        ) ?>
    </p>

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
                    <th width="25%" class="text-center"><?= Yii::t('backend', 'Действия') ?></th>
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
                        <td class="text-center">
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('backend', 'Ред.'),
                                ['/backend/login-servers/form', 'id' => $row->id],
                                [
                                    'class' => 'btn btn-xs btn-warning',
                                    'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;'
                                ]
                            ) ?>

                            <?= Html::a(
                                '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('backend', 'Удал.'),
                                ['/backend/login-servers/del', 'id' => $row->id],
                                [
                                    'class' => 'btn btn-xs btn-danger',
                                    'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                    'data-confirm' => Yii::t('backend', 'Удалить безвозвратно?'),
                                    'data-method'  => 'post',
                                ]
                            ) ?>

                            <?= Html::a(
                                '<i class="glyphicon ' . ($row->isStatusOn() ? 'glyphicon-eye-close' : 'glyphicon-eye-open') . '"></i> ' .
                                ($row->isStatusOn() ? Yii::t('backend', 'Выкл.') : Yii::t('backend', 'Вкл.')),
                                ['/backend/login-servers/allow', 'id' => $row->id],
                                [
                                    'class' => 'btn btn-xs ' . ($row->isStatusOn() ? 'btn-danger' : 'btn-success'),
                                    'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                    'data-confirm' => Yii::t('backend', 'Изменить статус?'),
                                    'data-method'  => 'post',
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>