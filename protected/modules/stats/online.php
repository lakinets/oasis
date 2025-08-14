<?php
/* @var $this yii\web\View */
/* @var $onlinePlayers array */
/* @var $server array */
?>

<table class="table">
    <thead>
        <tr>
            <th width="5%"><?= Yii::t('main', 'Место') ?></th>
            <th><?= Yii::t('main', 'Персонаж') ?></th>
            <th width="14%"><?= Yii::t('main', 'PvP/PK') ?></th>
            <th width="20%"><?= Yii::t('main', 'Клан') ?></th>
            <th width="21%"><?= Yii::t('main', 'Время в игре') ?></th>
            <th width="13%"><?= Yii::t('main', 'Статус') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($onlinePlayers)): ?>
            <?php foreach ($onlinePlayers as $i => $row): ?>
                <tr class="<?= $i % 2 == 0 ? 'odd' : 'even' ?>">
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?= Html::encode($row['char_name']) ?>
                        <p class="help-block" style="font-size: 13px;">
                            <?= $this->context->getClassName($row['base_class']) ?> [<?= $row['level'] ?>]
                        </p>
                    </td>
                    <td><?= $row['pvpkills'] ?>/<?= $row['pkkills'] ?></td>
                    <td>
                        <?php
                        if (empty($row['clan_name'])) {
                            echo '-';
                        } else {
                            echo Html::a(Html::encode($row['clan_name']), ['/stats/default/clan-info', 'clanId' => $row['clan_id']]);
                        }
                        ?>
                    </td>
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