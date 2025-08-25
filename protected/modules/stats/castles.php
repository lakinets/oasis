<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $castles array */
/* @var $server array */

/**
 * Локальная функция форматирования дат
 * timestamp может быть в секундах или миллисекундах
 */
if (!function_exists('formatDate')) {
    function formatDate($ts): string {
        if (empty($ts) || !is_numeric($ts)) {
            return '-';
        }
        // если timestamp слишком большой — значит в миллисекундах
        if ($ts > 2000000000) {
            $ts = (int)($ts / 1000);
        }
        return date('d.m.Y H:i', $ts);
    }
}
?>

<table class="table table-bordered table-striped">
    <tbody>
        <?php $i = 0; foreach ($castles as $row): ?>
            <tr>
                <th colspan="2"><?= Html::encode($row['name']) ?></th>
            </tr>
            <tr class="<?= ++$i % 2 == 0 ? 'odd' : 'even' ?>">
                <td width="150"><?= Html::encode($row['icon']) ?></td>
                <td>
                    <?= Yii::t('main', 'Налог') ?>: <i><?= (int)$row['tax_percent'] ?>%</i><br />
                    <?= Yii::t('main', 'Дата осады') ?>: <i><?= formatDate($row['sieg_date']) ?></i><br />
                    <?= Yii::t('main', 'Владелец') ?>:
                    <?php if (isset($row['owner'])): ?>
                        <?= $row['owner']
                            ? Html::a(Html::encode($row['owner']), [
                                '/stats/default/index',
                                'gs_id'   => $server['id'],
                                'type'    => 'clan-info',
                                'clan_id' => $row['owner_id']
                              ])
                            : '<i>NPC</i>' ?>
                    <?php endif; ?>
                    <br />

                    <?= Yii::t('main', 'Нападающие') ?>:
                    <?php
                    $forwards = $row['forwards'] ?? [];
                    $f = [];
                    if (!empty($forwards) && is_array($forwards)) {
                        foreach ($forwards as $fd) {
                            $f[] = $server['stats_clan_info']
                                ? Html::a(Html::encode($fd['clan_name']), [
                                    '/stats/default/index',
                                    'gs_id'   => $server['id'],
                                    'type'    => 'clan-info',
                                    'clan_id' => $fd['clan_id']
                                  ])
                                : Html::encode($fd['clan_name']);
                        }
                    } else {
                        $f[] = Yii::t('main', 'Нет');
                    }
                    echo implode(', ', $f);
                    ?>
                    <br />

                    <?= Yii::t('main', 'Защитники') ?>:
                    <?php
                    $defenders = $row['defenders'] ?? [];
                    $f = [];
                    if (!empty($defenders) && is_array($defenders)) {
                        foreach ($defenders as $fd) {
                            $f[] = $server['stats_clan_info']
                                ? Html::a(Html::encode($fd['clan_name']), [
                                    '/stats/default/index',
                                    'gs_id'   => $server['id'],
                                    'type'    => 'clan-info',
                                    'clan_id' => $fd['clan_id']
                                  ])
                                : Html::encode($fd['clan_name']);
                        }
                    } else {
                        $f[] = Yii::t('main', 'Нет');
                    }
                    echo implode(', ', $f);
                    ?>
                    <br />
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
