<?php
/**
 * @var yii\web\View $this
 * @var app\modules\backend\models\Bonuses $bonus
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\backend\models\BonusesItems[] $data
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

$title_ = Yii::t('backend', 'Бонусы');
$this->title = $title_;
$this->params['breadcrumbs'] = [
    ['label' => $title_, 'url' => ['/backend/bonuses/index']],
    $bonus->title,
];

\app\widgets\FlashMessages\FlashMessages::widget();
?>

<?= Html::a(Yii::t('backend', 'Добавить предмет'), ['/backend/bonuses/item-add', 'bonus_id' => $bonus->id], ['class' => 'btn btn-primary']) ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th width="5%"></th>
                <th><?= Yii::t('backend', 'Название') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Кол-во') ?></th>
                <th width="5%"><?= Yii::t('backend', 'Заточка') ?></th>
                <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                <th width="12%"></th>
            </tr>
        </thead>
        <tbody>
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'emptyText' => '<tr><td colspan="6">' . Yii::t('backend', 'Нет данных.') . '</td></tr>',
            'itemView' => function ($row) use ($bonus) {
                /** @var \app\modules\backend\models\BonusesItems $row */
                return '
                    <tr>
                        <td>' . ($row->itemInfo ? $row->itemInfo->getIcon() : '') . '</td>
                        <td>' . Html::encode($row->itemInfo ? $row->itemInfo->name : '-') . '</td>
                        <td>' . number_format($row->count, 0, '', '.') . '</td>
                        <td>' . $row->enchant . '</td>
                        <td>' . ($row->status ? Yii::t('backend', 'вкл') : Yii::t('backend', 'выкл')) . '</td>
                        <td>
                            <div class="btn-group btn-group-xs">
                                ' . Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['/backend/bonuses/item-edit', 'bonus_id' => $bonus->id, 'item_id' => $row->id], ['class' => 'btn btn-default', 'title' => Yii::t('backend', 'Редактировать')]) . '
                                ' . Html::a('<i class="glyphicon glyphicon-eye-' . ($row->status ? 'close' : 'open') . '"></i>', ['/backend/bonuses/item-allow', 'bonus_id' => $bonus->id, 'item_id' => $row->id], ['class' => 'btn btn-default', 'title' => ($row->status ? Yii::t('backend', 'Выключить') : Yii::t('backend', 'Включить'))]) . '
                                ' . Html::a('<i class="glyphicon glyphicon-trash"></i>', ['/backend/bonuses/item-del', 'bonus_id' => $bonus->id, 'item_id' => $row->id], ['class' => 'btn btn-danger', 'title' => Yii::t('backend', 'Удалить'), 'data-confirm' => Yii::t('backend', 'Удалить предмет?')]) . '
                            </div>
                        </td>
                    </tr>';
            },
            'options' => ['tag' => false],
            'itemOptions' => ['tag' => false],
        ]) ?>
        </tbody>
    </table>
</div>

<div class="text-center">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
    ]) ?>
</div>