<?php
/** @var int|null $online */
/** @var array|null $server */
/** @var bool $serverDown */
?>

<h1>Статистика сервера</h1>

<?php if ($serverDown): ?>
    <p><strong>Нет данных от сервера.</strong></p>
<?php else: ?>
    <p><strong>Сервер:</strong> <?= htmlspecialchars($server['name']) ?></p>
    <p><strong>IP:</strong> <?= $server['ip'] ?>:<?= $server['port'] ?></p>
    <p><strong>Онлайн:</strong> <?= $online ?></p>
<?php endif; ?>
