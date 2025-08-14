<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use app\modules\backend\models\Gs;

/** @var yii\web\View $this */
/** @var app\modules\backend\models\GsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('backend', 'Игровые сервера');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="gs-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('backend', 'Создать'), ['form'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['index']),
    ]); ?>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th><?= Yii::t('backend', 'Название') ?></th>
                    <th width="10%"><?= Yii::t('backend', 'IP') ?></th>
                    <th width="5%"><?= Yii::t('backend', 'Порт') ?></th>
                    <th width="15%"><?= Yii::t('backend', 'Версия') ?></th>
                    <th width="15%"><?= Yii::t('backend', 'Логин') ?></th>
                    <th width="10%"><?= Yii::t('backend', 'Статус') ?></th>
                    <th width="12%"><?= Yii::t('backend', 'Действия') ?></th>
                </tr>
                <tr>
                    <td><?= Html::activeTextInput($searchModel, 'id', ['class' => 'form-control input-sm']) ?></td>
                    <td><?= Html::activeTextInput($searchModel, 'name', ['class' => 'form-control input-sm']) ?></td>
                    <td><?= Html::activeTextInput($searchModel, 'ip', ['class' => 'form-control input-sm']) ?></td>
                    <td><?= Html::activeTextInput($searchModel, 'port', ['class' => 'form-control input-sm']) ?></td>
                    <td><?= Html::activeDropDownList($searchModel, 'version', Gs::getVersionList(), ['class' => 'form-control input-sm', 'prompt' => '']) ?></td>
                    <td><?= Html::activeDropDownList($searchModel, 'login_id', ArrayHelper::map(\app\modules\backend\models\LoginServers::find()->all(), 'id', 'name'), ['class' => 'form-control input-sm', 'prompt' => '']) ?></td>
                    <td><?= Html::activeDropDownList($searchModel, 'status', Gs::getStatusList(), ['class' => 'form-control input-sm', 'prompt' => '']) ?></td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-sm glyphicon glyphicon-search" title="<?= Yii::t('backend', 'Искать') ?>"></button>
                        <?= Html::a('', ['index'], ['class' => 'btn btn-default btn-sm glyphicon glyphicon-ban-circle', 'title' => Yii::t('backend', 'Сбросить')]) ?>
                    </td>
                </tr>
            </thead>

            <tbody>
                <?php if ($dataProvider->models): ?>
                    <?php foreach ($dataProvider->models as $row): ?>
                        <tr>
                            <td><?= $row->id ?></td>
                            <td><?= Html::encode($row->name) ?></td>
                            <td><?= $row->ip ?></td>
                            <td><?= $row->port ?></td>
                            <td><?= Html::encode($row->version ?: '-') ?></td>
                            <td><?= Html::encode($row->ls->name ?? '—') ?></td>
                            <td>
                                <?= $row->getStatus() ?>
                                <a href="/backend/game-servers/allow?gs_id=<?= $row->id ?>"
                                   class="btn btn-xs btn-default"
                                   title="<?= $row->isStatusOn() ? 'Выключить' : 'Включить' ?>">
                                    <?= $row->isStatusOn() ? 'Выкл' : 'Вкл' ?>
                                </a>
                            </td>
                            <td>
                                <a href="/backend/game-servers/shop?gs_id=<?= $row->id ?>">Магазин</a>
                                <a href="/backend/game-servers/form?gs_id=<?= $row->id ?>">Изменить</a>
                                <a href="/backend/game-servers/del?gs_id=<?= $row->id ?>">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8"><?= Yii::t('backend', 'Нет данных.') ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php ActiveForm::end(); ?>

    <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
</div>