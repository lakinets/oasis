<?php
/* @var $this yii\web\View */
/* @var $server array */
/* @var $countOnline int */
/* @var $countAccounts int */
/* @var $countCharacters int */
/* @var $countClans int */
/* @var $countMen int */
/* @var $countFemale int */
/* @var $races array */
/* @var $racesPercentage array */
?>

<!-- Разное -->
<table class="table">
    <tr class="divider">
        <td colspan="2"><?= Yii::t('main', 'Разное') ?></td>
    </tr>
    <tr>
        <td width="30%"><?= Yii::t('main', 'В игре') ?></td>
        <td width="70%"><?= $countOnline ?></td>
    </tr>
    <tr class="even">
        <td><?= Yii::t('main', 'Аккаунтов') ?></td>
        <td><?= $countAccounts ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('main', 'Персонажей') ?></td>
        <td><?= $countCharacters ?></td>
    </tr>
    <tr class="even">
        <td><?= Yii::t('main', 'Кланов') ?></td>
        <td><?= $countClans ?></td>
    </tr>
    <tr>
        <td><?= Yii::t('main', 'Мужчин') ?></td>
        <td>
            <?php
            $percent = $countMen + $countFemale > 0 ? ceil(($countMen / ($countMen + $countFemale)) * 100) : 0;
            ?>
            <div class="progress-bar">
                <span class="text"><?= $percent ?>% (<?= $countMen ?>)</span>
                <span class="line-bg" style="width: <?= $percent ?>%;"></span>
            </div>
        </td>
    </tr>
    <tr class="even">
        <td><?= Yii::t('main', 'Женщин') ?></td>
        <td>
            <?php
            $percent = $countMen + $countFemale > 0 ? ceil(($countFemale / ($countMen + $countFemale)) * 100) : 0;
            ?>
            <div class="progress-bar">
                <span class="text"><?= $percent ?>% (<?= $countFemale ?>)</span>
                <span class="line-bg" style="width: <?= $percent ?>%;"></span>
            </div>
        </td>
    </tr>
</table>

<!-- Расы -->
<table class="table stats-table">
    <tr class="divider">
        <td colspan="2"><?= Yii::t('main', 'Расы') ?></td>
    </tr>
    <tr>
        <td width="30%"><?= Yii::t('main', 'Люди') ?></td>
        <td width="70%">
            <div class="progress-bar">
                <span class="text"><?= $racesPercentage['human'] ?>% (<?= $races['human'] ?>)</span>
                <span class="line-bg" style="width: <?= $racesPercentage['human'] ?>%;"></span>
            </div>
        </td>
    </tr>
    <tr class="even">
        <td><?= Yii::t('main', 'Эльфы') ?></td>
        <td>
            <div class="progress-bar">
                <span class="text"><?= $racesPercentage['elf'] ?>% (<?= $races['elf'] ?>)</span>
                <span class="line-bg" style="width: <?= $racesPercentage['elf'] ?>%;"></span>
            </div>
        </td>
    </tr>
    <tr>
        <td><?= Yii::t('main', 'Темные Эльфы') ?></td>
        <td>
            <div class="progress-bar">
                <span class="text"><?= $racesPercentage['dark_elf'] ?>% (<?= $races['dark_elf'] ?>)</span>
                <span class="line-bg" style="width: <?= $racesPercentage['dark_elf'] ?>%;"></span>
            </div>
        </td>
    </tr>
    <tr class="even">
        <td><?= Yii::t('main', 'Орки') ?></td>
        <td>
            <div class="progress-bar">
                <span class="text"><?= $racesPercentage['ork'] ?>% (<?= $races['ork'] ?>)</span>
                <span class="line-bg" style="width: <?= $racesPercentage['ork'] ?>%;"></span>
            </div>
        </td>
    </tr>
    <tr>
        <td><?= Yii::t('main', 'Гномы') ?></td>
        <td>
            <div class="progress-bar">
                <span class="text"><?= $racesPercentage['dwarf'] ?>% (<?= $races['dwarf'] ?>)</span>
                <span class="line-bg" style="width: <?= $racesPercentage['dwarf'] ?>%;"></span>
            </div>
        </td>
    </tr>
    <?php if ($racesPercentage['kamael'] > 0): ?>
        <tr class="even">
            <td><?= Yii::t('main', 'Камаэли') ?></td>
            <td>
                <div class="progress-bar">
                    <span class="text"><?= $racesPercentage['kamael'] ?>% (<?= $races['kamael'] ?>)</span>
                    <span class="line-bg" style="width: <?= $racesPercentage['kamael'] ?>%;"></span>
                </div>
            </td>
        </tr>
    <?php endif; ?>
</table>

<!-- Рейты -->
<table class="table">
    <tr class="divider">
        <td colspan="2"><?= Yii::t('main', 'Рейты') ?></td>
    </tr>
    <tr>
        <td>Exp</td>
        <td><?= $server['exp'] ?></td>
    </tr>
    <tr class="even">
        <td>Sp</td>
        <td><?= $server['sp'] ?></td>
    </tr>
    <tr>
        <td>Adena</td>
        <td><?= $server['adena'] ?></td>
    </tr>
    <tr class="even">
        <td>Drop</td>
        <td><?= $server['drop'] ?></td>
    </tr>
    <tr>
        <td>Items</td>
        <td><?= $server['items'] ?></td>
    </tr>
    <tr class="even">
        <td>Spoil</td>
        <td><?= $server['spoil'] ?></td>
    </tr>
    <tr>
        <td>Quest drop</td>
        <td><?= $server['q_drop'] ?></td>
    </tr>
    <tr class="even">
        <td>Quest reward</td>
        <td><?= $server['q_reward'] ?></td>
    </tr>
    <tr>
        <td>Raid boss</td>
        <td><?= $server['rb'] ?></td>
    </tr>
    <tr class="even">
        <td>Epic Raid boss</td>
        <td><?= $server['erb'] ?></td>
    </tr>
</table>