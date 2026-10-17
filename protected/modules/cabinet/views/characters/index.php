<ul class="nav-mini">
    <li><a href="/cabinet/tickets"><?= Yii::t('views', 'Поддержка') ?></a></li>
    <li><a href="/cabinet/characters"><?= Yii::t('views', 'Персонажи') ?></a></li>
    <li><a href="/cabinet/shop"><?= Yii::t('views', 'Магазин') ?></a></li>
    <li><a href="/cabinet/bonuses"><?= Yii::t('views', 'Бонусы') ?></a></li>
    <li><a href="/cabinet/security"><?= Yii::t('views', 'Безопасность') ?></a></li>
    <li><a href="/cabinet/messages"><?= Yii::t('views', 'Сообщения') ?></a></li>
    <li><a href="/cabinet/deposit"><?= Yii::t('views', 'Пополнение') ?></a></li>
    <li><a href="/cabinet/transaction-history"><?= Yii::t('views', 'История транзакций') ?></a></li>
    <li><a href="/cabinet/auth-history"><?= Yii::t('views', 'История входов') ?></a></li>
    <li><a href="/cabinet/referals"><?= Yii::t('views', 'Рефералы') ?></a></li>
    <li><a href="/cabinet/services"><?= Yii::t('views', 'Услуги') ?></a></li>
</ul>
<?php
/** @var \app\models\Gs[] $servers */
/** @var int|null $gs_id */
/** @var array $characters */
/** @var string|null $error */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('views', 'Мои персонажи');
?>

<h1 class="orion-table-header"><?= Html::encode($this->title) ?></h1>

<!-- Кнопки серверов -->
<div style="margin: 10px 0;">
    <?php if ($servers): ?>
        <?php foreach ($servers as $s): ?>
            <a class="btn btn-sm <?= ($gs_id == $s->id ? 'btn-primary btn-orion' : 'btn-default btn-orion') ?>"
               href="<?= Url::to(['index', 'gs_id' => $s->id]) ?>">
                <?= Html::encode($s->name) ?>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning"><?= Yii::t('views', 'Нет активных игровых серверов.') ?></div>
    <?php endif; ?>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= Html::encode($error) ?></div>
<?php endif; ?>

<?php if (!$servers || $gs_id === null): ?>
    <div class="alert alert-info"><?= Yii::t('views', 'Выберите сервер, чтобы увидеть персонажей.') ?></div>
    <?php return; ?>
<?php endif; ?>

<?php if (empty($characters)): ?>
    <div class="alert alert-info"><?= Yii::t('views', 'У вас нет персонажей на выбранном сервере.') ?></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th class="orion-table-header"><?= Yii::t('views', 'Имя') ?></th>
                <th class="orion-table-header"><?= Yii::t('views', 'Уровень') ?></th>
                <th class="orion-table-header"><?= Yii::t('views', 'Клан') ?></th>
                <th class="orion-table-header"><?= Yii::t('views', 'Статус') ?></th>
                <th class="orion-table-header"><?= Yii::t('views', 'Время онлайн') ?></th>
                <th width="120" class="orion-table-header"><?= Yii::t('views', 'Действия') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($characters as $c): ?>
                <tr>
                    <td><?= Html::encode($c['char_name'] ?? '-') ?></td>
                    <td><?= Html::encode($c['level'] ?? '-') ?></td>
                    <td><?= Html::encode($c['clan_name'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($c['online'])): ?>
                            <span class="label label-success"><?= Yii::t('views', 'В игре') ?></span>
                        <?php else: ?>
                            <span class="label label-default"><?= Yii::t('views', 'Оффлайн') ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= \app\components\Lineage::getOnlineTime((int)($c['onlinetime'] ?? 0)) ?></td>
                    <td>
                        <a class="btn btn-xs btn-primary btn-orion"
                           href="<?= Url::to(['view', 'gs_id' => $gs_id, 'char_id' => $c['char_id']]) ?>"><?= Yii::t('views', 'Просмотр') ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
