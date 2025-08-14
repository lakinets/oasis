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

    <!-- вывод флеш-сообщений -->
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type ?>"><?= $message ?></div>
    <?php endforeach; ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Добавить новую категорию'), ['category-form'], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="5%">ID</th>
                <th><?= Yii::t('backend', 'Название') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                <th width="14%"><?= Yii::t('backend', 'Сортировка') ?></th>
                <th width="10%"></th>
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
                        <td>
                            <ul class="list-inline actions">
                                <li>
                                    <?= Html::a(
                                        '<span class="glyphicon glyphicon-pencil"></span>',
                                        ['category-form', 'id' => $row->id],
                                        ['title' => Yii::t('backend', 'Редактировать'), 'data-toggle' => 'tooltip']
                                    ) ?>
                                </li>
                                <li>
                                    <?= Html::a(
                                        '<span class="glyphicon ' . ($row->isStatusOn() ? 'glyphicon-eye-close' : 'glyphicon-eye-open') . '"></span>',
                                        ['category-allow', 'id' => $row->id],
                                        [
                                            'title' => $row->isStatusOn()
                                                ? Yii::t('backend', 'Выключить')
                                                : Yii::t('backend', 'Включить'),
                                            'data-toggle' => 'tooltip',
                                            'data-confirm' => Yii::t('backend', 'Вы уверены?')
                                        ]
                                    ) ?>
                                </li>
                                <li>
                                    <?= Html::a(
                                        '<span class="glyphicon glyphicon-remove"></span>',
                                        ['category-del', 'id' => $row->id],
                                        [
                                            'title' => Yii::t('backend', 'Удалить'),
                                            'data-toggle' => 'tooltip',
                                            'data-confirm' => Yii::t('backend', 'Удалить категорию безвозвратно?')
                                        ]
                                    ) ?>
                                </li>
                            </ul>
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