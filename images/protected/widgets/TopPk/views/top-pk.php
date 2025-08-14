<?php
use yii\helpers\Html;

if (!empty($data)): ?>
    <table cellpadding="0" cellspacing="0" id="l2pk">
        <?php foreach ($data as $row): ?>
            <tr>
                <td align="left"><?= Html::encode($row['char_name']) ?></td>
                <td align="right"><?= Html::encode($row['pvpkills']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p><?= \Yii::t('main', 'Нет данных о PK') ?></p>
<?php endif; ?>