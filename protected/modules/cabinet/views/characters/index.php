<ul class="nav-mini">
    <li><a href="/cabinet/tickets">Поддержка</a></li>
    <li><a href="/cabinet/characters">Персонажи</a></li>
    <li><a href="/cabinet/shop">Магазин</a></li>
    <li><a href="/cabinet/bonuses">Бонусы</a></li>
    <li><a href="/cabinet/security">Безопасность</a></li>
    <li><a href="/cabinet/messages">Сообщения</a></li>
    <li><a href="/cabinet/deposit">Пополнение</a></li>
    <li><a href="/cabinet/transaction-history">История транзакций</a></li>
    <li><a href="/cabinet/auth-history">История входов</a></li>
    <li><a href="/cabinet/referals">Рефералы</a></li>
    <li><a href="/cabinet/services">Услуги</a></li>
</ul>
<?php
/** @var \app\models\Gs[] $servers */
/** @var int|null $gs_id */
/** @var array $characters */
/** @var string|null $error */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Мои персонажи';
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
        <div class="alert alert-warning">Нет активных игровых серверов.</div>
    <?php endif; ?>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= Html::encode($error) ?></div>
<?php endif; ?>

<?php if (!$servers || $gs_id === null): ?>
    <div class="alert alert-info">Выберите сервер, чтобы увидеть персонажей.</div>
    <?php return; ?>
<?php endif; ?>

<?php if (empty($characters)): ?>
    <div class="alert alert-info">У вас нет персонажей на выбранном сервере.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th class="orion-table-header">Имя</th>
                <th class="orion-table-header">Уровень</th>
                <th class="orion-table-header">Клан</th>
                <th class="orion-table-header">Статус</th>
                <th class="orion-table-header">Время онлайн</th>
                <th width="120" class="orion-table-header">Действия</th>
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
                            <span class="label label-success">В игре</span>
                        <?php else: ?>
                            <span class="label label-default">Оффлайн</span>
                        <?php endif; ?>
                    </td>
                    <td><?= \app\components\Lineage::getOnlineTime((int)($c['onlinetime'] ?? 0)) ?></td>
                    <td>
                        <a class="btn btn-xs btn-primary btn-orion"
                           href="<?= Url::to(['view', 'gs_id' => $gs_id, 'char_id' => $c['char_id']]) ?>">
                            Просмотр
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
