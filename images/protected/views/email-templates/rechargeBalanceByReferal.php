<?php
/**
 * @var string $profit
 */
?>
<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Ваш баланс был пополнен на :profit по партнерской программе.', array(
    ':profit' => '<font color="#ead255"><b>' . formatCurrency($profit) . '</b></font>',
)) ?>
<br />
<?php echo Yii::t('main', 'Спасибо за Ваше участие.') ?>