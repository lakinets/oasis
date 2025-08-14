<?php if (!empty($data)): ?>
    <ul class="top-pk-list">
        <?php foreach ($data as $i => $row): ?>
            <li>
                <?= ($i + 1) ?>. <?= htmlspecialchars($row['char_name']) ?> — <?= (int)$row['pkkills'] ?> PK
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Нет данных о PVP</p>
<?php endif; ?>
