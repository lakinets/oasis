<table class="table">
    <thead>
        <tr>
            <th width="5%"><?php echo Yii::t('main', 'Место') ?></th>
            <th width="30%"><?php echo Yii::t('main', 'Название') ?></th>
            <th width="10%"><?php echo Yii::t('main', 'Уровень') ?></th>
            <th width="15%"><?php echo Yii::t('main', 'Замок') ?></th>
            <th width="10%"><?php echo Yii::t('main', 'Игроков') ?></th>
            <th width="10%"><?php echo Yii::t('main', 'Репутация') ?></th>
            <th width="20%"><?php echo Yii::t('main', 'Альянс') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if($content) { ?>
            <?php foreach($content as $i => $row) { ?>
                <tr class="<?php echo $i % 2 == 0 ? 'odd' : 'even' ?>">
                    <td><?php echo ++$i ?></td>
                    <td>
                        <?php echo clanAllyCrest('ally', $row['ally_id'], $this->_gs_id, $row['ally_crest']) .
                            clanAllyCrest('clan', $row['clan_id'], $this->_gs_id, $row['clan_crest']) ?>
                        <?php
                        if($this->_gs->stats_clan_info)
                        {
                            echo HTML::link($row['clan_name'], array('/stats/default/index', 'gs_id' => $this->_gs_id, 'type' => 'clan-info', 'clan_id' => $row['clan_id']));
                        }
                        else
                        {
                            echo '<font color="#9D6A1E">' . e($row['clan_name']) . '</font>';
                        }
                        ?>
                        <p class="help-block"><?php echo Yii::t('main', 'Лидер') ?>: <?php echo $row['char_name'] ?> [<?php echo Lineage::getClassName($row['base_class']) ?>][<?php echo $row['level'] ?>]</p>
                    </td>
                    <td><?php echo $row['clan_level'] ?></td>
                    <td><?php echo ($row['hasCastle'] != 0 ? Lineage::getCastleName($row['hasCastle']) : '-') ?></td>
                    <td><?php echo $row['ccount'] ?></td>
                    <td><?php echo number_format($row['reputation_score'], 0, '', '.') ?></td>
                    <td>
                        <?php echo ($row['ally_name'] != '' ? e($row['ally_name']) : '-') ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7"><?php echo Yii::t('main', 'Данных нет') ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>