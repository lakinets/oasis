<?php
/** @var array $castles */
use yii\helpers\Html;

$castles = is_array($castles) ? $castles : [];
if (!$castles) {
    echo "<em>Нет данных по замкам</em>";
    return;
}
?>
<table border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr>
            <th>#</th>
            <th>Название</th>
            <th>Владелец</th>
            <th>Прочее</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($castles as $i => $row): ?>
        <?php
            // Подстраиваемся под разные схемы
            $name     = $row['name']        ?? $row['castle_name'] ?? ('Castle #' . ($row['id'] ?? $row['castle_id'] ?? ($i+1)));
            $ownerId  = $row['owner_id']    ?? $row['id_own']      ?? $row['clan_id'] ?? null;

            // Собираем "прочее"
            $extra = [];
            foreach (['castle_id','taxPercent','tax_percent','siegeDate','siege_date'] as $k) {
                if (isset($row[$k])) {
                    $extra[] = Html::encode($k . ': ' . $row[$k]);
                }
            }
        ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= Html::encode($name) ?></td>
            <td><?= $ownerId !== null ? (int)$ownerId : '-' ?></td>
            <td><?= $extra ? implode(', ', $extra) : '-' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
