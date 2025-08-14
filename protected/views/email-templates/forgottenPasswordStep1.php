<?php
/**
 * @var string $hash
 */
$url      = $this->createAbsoluteUrl('/forgottenPassword/default/step2', array('hash' => $hash));
$siteName = CHtml::link($_SERVER['HTTP_HOST'], $this->createAbsoluteUrl('/index/default/index'));
?>

<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Вы подали заявку на восстановление пароля на сайте :site_name', array(':site_name' => '<font color="#5aaee9">' . $siteName . '</font>')) ?>
<br />
<?php echo Yii::t('main', 'Ваша <a href=":link"><font color="#5aaee9">ссылка</font></a> для восстановления пароля от аккаунта', array(':link' => $url)) ?>
<br />
<?php echo Yii::t('main', 'Или скопируйте ссылку ниже и вставьте в адресную строку Вашего браузера') ?>
<br />
<?php echo $url ?>