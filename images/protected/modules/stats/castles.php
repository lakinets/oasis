<?php
/* @var $this yii\web\View */
/* @var $castles array */
/* @var $server array */
?>

<table class="table">
    <tbody>
        <?php $i = 0; foreach ($castles as $row): ?>
            <tr>
                <th colspan="2"><?= $row['name'] ?></th> <!-- замените на соответствующее поле -->
            </tr>
            <tr class="<?= ++$i % 2 == 0 ? 'odd' : 'even' ?>">
                <td width="150"><?= $row['icon'] ?></td> <!-- замените на соответствующее поле -->
                <td>
                    <?= Yii::t('main', 'Налог') ?>: <i><?= $row['tax_percent'] ?>%</i><br />
                    <?= Yii::t('main', 'Дата осады') ?>: <i><?= formatDate($row['sieg_date']) ?></i><br />
                    <?= Yii::t('main', 'Владелец') ?>: <?= isset($row['owner']) ? ($row['owner'] ? Html::a($row['owner'], ['/stats/default/index', 'gs_id' => $server['id'], 'type' => 'clan-info', 'clan_id' => $row['owner_id']]) : '<i>NPC</i>') : '' ?> <br />
                    <?= Yii::t('main', 'Нападающие') ?>:
                    <?php
                    $forwards = $row['forwards'] ?? [];
                    $f = array();
                    if (!empty($forwards) && is_array($forwards)) {
                        foreach ($forwards as $fd) {
                            if ($server['stats_clan_info']) {
                                $f[] = Html::a($fd['clan_name'], ['/stats/default/index', 'gs_id' => $server['id'], 'type' => 'clan-info', 'clan_id' => $fd['clan_id']]);
                            } else {
                                $f[] = $fd['clan_name'];
                            }
                        }
                    } else {
                        $f[] = Yii::t('main', 'Нет');
                    }
                    echo implode(', ', $f);
                    ?> <br />
                    <?= Yii::t('main', 'Защитники') ?>:
                    <?php
                    $defenders = $row['defenders'] ?? [];
                    $f = array();
                    if (!empty($defenders) && is_array($defenders)) {
                        foreach ($defenders as $fd) {
                            if ($server['stats_clan_info']) {
                                $f[] = Html::a($fd['clan_name'], ['/stats/default/index', 'gs_id' => $server['id'], 'type' => 'clan-info', 'clan_id' => $fd['clan_id']]);
                            } else {
                                $f[] = $fd['clan_name'];
                            }
                        }
                    } else {
                        $f[] = Yii::t('main', 'Нет');
                    }
                    echo implode(', ', $f);
                    ?> <br />
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>