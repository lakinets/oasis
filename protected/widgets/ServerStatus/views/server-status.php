<?php if (!empty($content)): ?>
    <div class="panel panel-default server-status">
        <div class="panel-heading">Статус серверов</div>
        <div class="panel-body">
            <?php foreach ($content as $id => $row): ?>
                <div>
                    <strong><?= htmlspecialchars($row['gs']->name) ?></strong><br>
                    Game: <?= $row['gs_status'] ?><br>
                    Login: <?= $row['ls_status'] ?><br>
                    Online: <?= $row['online'] ?>
                    <?php if (isset($row['error'])): ?>
                        <br><span class="text-danger"><?= $row['error'] ?></span>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endforeach; ?>
            <p><strong>Всего онлайн: <?= $totalOnline ?></strong></p>
        </div>
    </div>
<?php endif; ?>