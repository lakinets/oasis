<?php
/* @var $servers \app\models\Gs[] */
/* @var $content string */
?>
<style>
    .server-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
    .server-list a { padding: 6px 12px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; }
    .server-list a.active { background: #0056b3; }
</style>

<h1>Статистика серверов</h1>

<div class="server-list">
    <?php foreach ($servers as $s): ?>
        <a href="#" data-id="<?= $s->id ?>"
           class="<?= $s->id == ($content ? $s->id : 0) ? 'active' : '' ?>">
            <?= htmlspecialchars($s->name) ?>
        </a>
    <?php endforeach; ?>
</div>

<div id="stat-content">
    <?= $content ?>
</div>

<script>
document.addEventListener('click', function (e) {
    if (!e.target.matches('.server-list a')) return;
    e.preventDefault();
    const id = e.target.dataset.id;
    fetch('<?= Url::to(['index']) ?>?gs_id=' + id)
        .then(r => r.text())
        .then(html => document.getElementById('stat-content').innerHTML = html);
});
</script>