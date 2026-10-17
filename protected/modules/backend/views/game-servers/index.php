<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\GsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('backend', 'Игровые сервера');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="game-servers-index">
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
                <th width="12%">IP</th>
                <th width="7%"><?= Yii::t('backend', 'Порт') ?></th>
                <th width="12%"><?= Yii::t('backend', 'Версия') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                <th width="36%" class="text-center"><?= Yii::t('backend', 'Действия') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->models as $row): ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= Html::encode($row->name) ?></td>
                    <td><?= Html::encode($row->ip) ?></td>
                    <td><?= (int)$row->port ?></td>
                    <td><?= Html::encode($row->version) ?></td>
                    <td>
                        <span class="label <?= $row->isStatusOn() ? 'label-success' : 'label-default' ?>">
                            <?= $row->getStatusLabel() ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?= Html::a(
                            '<i class="glyphicon glyphicon-shopping-cart"></i> ' . Yii::t('backend', 'Магазин'),
                            ['shop', 'gs_id' => $row->id],
                            [
                                'class' => 'btn btn-xs btn-primary',
                                'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;'
                            ]
                        ) ?>

                        <?= Html::a(
                            '<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('backend', 'Ред.'),
                            ['form', 'gs_id' => $row->id],
                            [
                                'class' => 'btn btn-xs btn-warning',
                                'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;'
                            ]
                        ) ?>

                        <?= Html::a(
                            '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('backend', 'Удал.'),
                            ['del', 'gs_id' => $row->id],
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
                            ['allow', 'gs_id' => $row->id],
                            [
                                'class' => 'btn btn-xs ' . ($row->isStatusOn() ? 'btn-danger' : 'btn-success'),
                                'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                'data-confirm' => Yii::t('backend', 'Изменить статус?'),
                                'data-method'  => 'post', // из кнопки — POST, по прямой ссылке GET тоже сработает
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

