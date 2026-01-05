<?php
/* @var array $stats */
foreach ($stats as $s): ?>
    <div class="tr">
        <div class="tr_title" style="color:<?=
            $s['color'] === 'green'  ? '#47ae58' :
            ($s['color'] === 'yellow'? '#dbb054' : '#cf4a40') ?>">
            <span><?= htmlspecialchars($s['race']) ?></span>
            <?= $s['percent'] ?>%
        </div>
        <div class="tr_bar">
            <div class="progress_<?= $s['color'] ?>" style="width:<?= $s['percent'] ?>%"></div>
        </div>
    </div>
<?php endforeach; ?>