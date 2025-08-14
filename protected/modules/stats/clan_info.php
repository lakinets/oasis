<?php
/* @var $this yii\web\View */
/* @var $clanInfo array */
/* @var $clanCharacters array */
/* @var $server array */
?>

<p>
    <?= Yii::t('main', 'Состав клана') ?>: <?= $clanInfo['clan_name'] ?><br>
    <?= Yii::t('main', 'Алли') ?>: <?= $clanInfo['ally_name'] ? Html::encode($clanInfo['ally_name']) : Yii::t('main', 'Нет') ?><br>
    <?= Yii::t('main', 'Замок') ?>: <?= $clanInfo['hasCastle'] ? $clanInfo['hasCastleName'] : Yii::t('main', 'Нет') ?><br>
    <?= Yii::t('main', 'Лидер') ?>: <?= Html::encode($clanInfo['char_name']) ?> (<?= $clanInfo['className'] ?> <?= $clanInfo['level'] ?>)
</p>

<table class="table">
    <thead>
        <tr>
            <th width="5%"><?= Yii::t('main', 'Место') ?></th>
            <th><?= Yii::t('main', 'Персонаж') ?></th>
            <th width="14%"><?= Yii::t('main', 'PvP/PK') ?></th>
            <th width="21%"><?= Yii::t('main', 'Время в игре') ?></th>
            <th width="13%"><?= Yii::t('main', 'Статус') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($clanCharacters)): ?>
            <?php foreach ($clanCharacters as $i => $row): ?>
                <tr class="<?= $i % 2 == 0 ? 'odd' : 'even' ?>">
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?= Html::encode($row['char_name']) ?>
                        <p class="help-block" style="font-size: 13px;">
                            <?= $row['className'] ?> [<?= $row['level'] ?>]
                        </p>
                    </td>
                    <td><?= $row['pvpkills'] ?>/<?= $row['pkkills'] ?></td>
                    <td><?= $this->context->getOnlineTime($row['onlinetime']) ?></td>
                    <td>
                        <?= $row['online'] 
                            ? '<span class="status-online" title="' . Yii::t('main', 'В игре') . '"></span>' 
                            : '<span class="status-offline" title="' . Yii::t('main', 'Не в игре') . '"></span>' 
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6"><?= Yii::t('main', 'Данных нет') ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>