<?php
/**
 * Компактный вывод (как в заглушке макета)
 * @var array  $content
 * @var string $assetsUrl
 */
?>
<div id="status">
    <?php foreach ($content as $row): ?>
        <div class="server">
            <a href="#"><?= htmlspecialchars($row['gs']->name) ?></a>
            <img src="<?= $assetsUrl ?>/images/<?= $row['gs_status'] === 'online' ? 'online' : 'offline' ?>.png" alt="">
            <?= $row['online'] ?>
        </div>
    <?php endforeach; ?>
</div>