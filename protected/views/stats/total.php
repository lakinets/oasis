<?php
/** @var int $countAccounts */
/** @var int $countCharacters */
/** @var int $countClans */
/** @var int $countOnline */

// Без "undefined variable"
$countAccounts   = isset($countAccounts)   ? (int)$countAccounts   : 0;
$countCharacters = isset($countCharacters) ? (int)$countCharacters : 0;
$countClans      = isset($countClans)      ? (int)$countClans      : 0;
$countOnline     = isset($countOnline)     ? (int)$countOnline     : 0;
?>
<div>
    <p>Аккаунтов: <?= $countAccounts ?></p>
    <p>Персонажей: <?= $countCharacters ?></p>
    <p>Кланов: <?= $countClans ?></p>
    <p>Онлайн: <?= $countOnline ?></p>
</div>
