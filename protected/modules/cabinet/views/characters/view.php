<?php
/** @var \app\models\Gs[] $servers */
/** @var int $gs_id */
/** @var array $character */
/** @var array $items */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Персонаж: ' . ($character['char_name'] ?? '—');
?>

<h1><?= Html::encode($this->title) ?></h1>

<!-- Кнопки серверов -->
<div style="margin: 10px 0;">
    <?php foreach ($servers as $s): ?>
        <a class="btn btn-sm <?= ($gs_id == $s->id ? 'btn-primary' : 'btn-default') ?>"
           href="<?= Url::to(['/cabinet/characters/index', 'gs_id' => $s->id]) ?>">
            <?= Html::encode($s->name) ?>
        </a>
    <?php endforeach; ?>
    <a class="btn btn-sm btn-default" href="<?= Url::to(['/cabinet/characters/index', 'gs_id' => $gs_id]) ?>">
        ← Назад к списку
    </a>
</div>

<!-- Основная информация -->
<div class="panel panel-default">
    <div class="panel-heading">Общая информация</div>
    <div class="panel-body">
        <div><b>Имя:</b> <?= Html::encode($character['char_name'] ?? '-') ?></div>
        <div><b>Уровень:</b> <?= Html::encode($character['level'] ?? '-') ?></div>
        <div><b>Клан:</b> <?= Html::encode($character['clan_name'] ?? '-') ?></div>
        <div><b>Титул:</b> <?= Html::encode($character['title'] ?? '-') ?></div>
        <div><b>PvP / PK:</b> <?= (int)($character['pvpkills'] ?? 0) ?> / <?= (int)($character['pkkills'] ?? 0) ?></div>
        <div><b>Статус:</b>
            <?php if (!empty($character['online'])): ?>
                <span class="label label-success">В игре</span>
            <?php else: ?>
                <span class="label label-default">Оффлайн</span>
            <?php endif; ?>
        </div>
        <div><b>Онлайн времени:</b> <?= \app\components\Lineage::getOnlineTime((int)($character['onlinetime'] ?? 0)) ?></div>
        <div><b>Координаты:</b>
            X: <?= Html::encode($character['x'] ?? '-') ?>,
            Y: <?= Html::encode($character['y'] ?? '-') ?>,
            Z: <?= Html::encode($character['z'] ?? '-') ?>
        </div>
    </div>
</div>

<!-- Инвентарь -->
<div class="panel panel-default">
    <div class="panel-heading">Инвентарь</div>
    <div class="panel-body">
        <?php if (empty($items)): ?>
            <div class="alert alert-info">Инвентарь пуст.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th width="100">Item ID</th>
                        <th>Название</th>
                        <th width="120">Кол-во</th>
                        <th width="120">Локация</th>
                        <th width="120">Слот</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= (int)$it['item_id'] ?></td>
                            <td><?= Html::encode($it['name'] ?? '(неизвестно)') ?></td>
                            <td><?= (int)($it['count'] ?? 1) ?></td>
                            <td><?= Html::encode($it['loc'] ?? '-') ?></td>
                            <td><?= Html::encode($it['slot'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
