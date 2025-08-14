<?php
/**
 * @var string $password
 */
$siteName = CHtml::link($_SERVER['HTTP_HOST'], $this->createAbsoluteUrl('/index/default/index'));
?>

<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Вы сменили пароль от аккаунта на сайте :site_name', array(':site_name' => '<font color="#5aaee9">' . $siteName . '</font>')) ?>
<br />
<?php echo Yii::t('main', 'Ваш новый пароль') ?>: <font color="#ead255"><?php echo $password ?></font>