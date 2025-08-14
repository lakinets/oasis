<?php
/* @var $this yii\web\View */
/* @var $items array */
/* @var $server array */
?>

<?php foreach ($items as $itemId => $row): ?>
    <table class="table">
        <thead>
            <tr>
                <th colspan="7"><?= Html::encode($row['itemInfo']->name) ?> (<?= Yii::t('main', 'общее кол-во') ?>: <?= number_format($row['maxTotalItems'], 0, '', '.') ?>, <?= Yii::t('main', 'кол-во персонажей') ?>: <?= count($row['characters']) ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($row['characters'])): ?>
                <tr>
                    <td width="5%">#</td>
                    <td><?= Yii::t('main', 'Персонаж') ?></td>
                    <td width="15%"><?= Yii::t('main', 'PvP/PK') ?></td>
                    <td width="20%"><?= Yii::t('main', 'Клан') ?></td>
                    <td width="15%"><?= Yii::t('main', 'Время в игре') ?></td>
                    <td width="10%"><?= Yii::t('main', 'Статус') ?></td>
                    <td width="15%"><?= Yii::t('main', 'Кол-во') ?></td>
                </tr>
                <?php foreach ($row['characters'] as $i => $character): ?>
                    <tr<?= $i % 2 == 0 ? ' class="even"' : '' ?>>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?= Html::encode($character['char_name']) ?>
                            <p style="font-size: 13px;" class="help-block">
                                <?= $character['className'] ?> [<?= $character['level'] ?>]
                            </p>
                        </td>
                        <td><?= $character['pvpkills'] ?>/<?= $character['pkkills'] ?></td>
                        <td>
                            <?php
                            if (empty($character['clan_name'])) {
                                echo Yii::t('main', 'Не в клане');
                            } else {
                                echo Html::a(Html::encode($character['clan_name']), ['/stats/default/clan-info', 'clanId' => $character['clan_id']]);
                            }
                            ?>
                        </td>
                        <td><?= $this->context->getOnlineTime($character['onlinetime']) ?></td>
                        <td>
                            <?= $character['online'] 
                                ? '<span class="status-online" title="' . Yii::t('main', 'В игре') . '"></span>' 
                                : '<span class="status-offline" title="' . Yii::t('main', 'Не в игре') . '"></span>' 
                            ?>
                        </td>
                        <td><?= number_format($character['maxCountItems'], 0, '', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7"><?= Yii::t('main', 'Владельцев нет') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endforeach; ?>