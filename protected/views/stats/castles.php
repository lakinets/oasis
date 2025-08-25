<?php
/** @var array $castles */
use yii\helpers\Html;

$castles = is_array($castles) ? $castles : [];
if (!$castles) {
    echo "<em>Нет данных по замкам</em>";
    return;
}

/**
 * Форматирование даты
 * Поддержка timestamp в секундах и миллисекундах
 */
function formatDate($ts): string {
    if (!$ts || !is_numeric($ts)) {
        return '-';
    }
    if ($ts > 2000000000) { // миллисекунды
        $ts = (int)($ts / 1000);
    }
    return date('d.m.Y H:i', $ts);
}

// Человеческие названия для ключей
$labels = [
    'castle_id'   => 'ID замка',
    'taxPercent'  => 'Налог',
    'tax_percent' => 'Налог',
    'siegeDate'   => 'Дата осады',
    'siege_date'  => 'Дата осады',
];
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
            $name    = $row['name'] ?? $row['castle_name'] ?? ('Castle #' . ($row['id'] ?? $row['castle_id'] ?? ($i+1)));
            $ownerId = $row['owner_id'] ?? $row['id_own'] ?? $row['clan_id'] ?? null;

            // Собираем "прочее"
            $extra = [];
            foreach ($labels as $k => $label) {
                if (isset($row[$k])) {
                    $val = $row[$k];
                    if (stripos($k, 'siege') !== false) {
                        $val = formatDate($val);
                    } elseif (stripos($k, 'tax') !== false) {
                        $val = (int)$val . '%';
                    }
                    $extra[] = $label . ': ' . Html::encode($val);
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
