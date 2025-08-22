<?php
/** @var array $clans */
use yii\helpers\Html;

$clans = is_array($clans) ? $clans : [];
if (!$clans) {
    echo "<em>Нет данных по кланам</em>";
    return;
}
?>
<table border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr>
            <th>#</th>
            <th>Клан</th>
            <th>Репутация</th>
            <th>Уровень</th>
            <th>ID</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($clans as $i => $row): ?>
        <?php
            // Пробуем покрыть разные схемы
            $name  = $row['clan_name'] ?? $row['name'] ?? ('Clan #' . ($row['clan_id'] ?? $row['id'] ?? ($i+1)));
            $rep   = $row['reputation_score'] ?? $row['reputation'] ?? null;
            $level = $row['level'] ?? $row['lvl'] ?? null;
            $id    = $row['clan_id'] ?? $row['id'] ?? null;
        ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= Html::encode($name) ?></td>
            <td><?= $rep !== null ? (int)$rep : '-' ?></td>
            <td><?= $level !== null ? (int)$level : '-' ?></td>
            <td><?= $id !== null ? (int)$id : '-' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
