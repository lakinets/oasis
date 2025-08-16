<?php
/**
 * @var yii\web\View                 $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$title_ = Yii::t('backend', 'Тикеты - категории');
$this->title = $title_;
$this->params['breadcrumbs'][] = $title_;
?>
<div class="tickets-categories">

    <!-- флеш-сообщения -->
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type ?>"><?= $message ?></div>
    <?php endforeach; ?>

    <p>
        <?= Html::a(
            '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('backend', 'Добавить новую категорию'),
            ['category-form'],
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="5%">ID</th>
                <th><?= Yii::t('backend', 'Название') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Сортировка') ?></th>
                <th width="25%" class="text-center"><?= Yii::t('backend', 'Действия') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($models = $dataProvider->models): ?>
                <?php foreach ($models as $row): ?>
                    <tr>
                        <td><?= $row->id ?></td>
                        <td><?= Html::encode($row->title) ?></td>
                        <td>
                            <span class="label <?= $row->isStatusOn() ? 'label-success' : 'label-default' ?>">
                                <?= $row->getStatus() ?>
                            </span>
                        </td>
                        <td><?= $row->sort ?></td>
                        <td class="text-center">
                            <!-- Редактировать -->
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('backend', 'Ред.'),
                                ['category-form', 'id' => $row->id],
                                [
                                    'class' => 'btn btn-xs btn-warning',
                                    'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;'
                                ]
                            ) ?>

                            <!-- Включить / Выключить -->
                            <?= Html::a(
                                '<i class="glyphicon ' . ($row->isStatusOn() ? 'glyphicon-eye-close' : 'glyphicon-eye-open') . '"></i> ' .
                                ($row->isStatusOn() ? Yii::t('backend', 'Выкл.') : Yii::t('backend', 'Вкл.')),
                                ['category-allow', 'id' => $row->id],
                                [
                                    'class' => 'btn btn-xs ' . ($row->isStatusOn() ? 'btn-danger' : 'btn-success'),
                                    'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                    'data-confirm' => Yii::t('backend', 'Изменить статус?'),
                                    'data-method'  => 'post',
                                ]
                            ) ?>

                            <!-- Удалить -->
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('backend', 'Удал.'),
                                ['category-del', 'id' => $row->id],
                                [
                                    'class' => 'btn btn-xs btn-danger',
                                    'style' => 'padding:2px 6px;font-size:11px;line-height:1.2;',
                                    'data-confirm' => Yii::t('backend', 'Удалить безвозвратно?'),
                                    'data-method'  => 'post',
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5"><?= Yii::t('backend', 'Нет данных.') ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>