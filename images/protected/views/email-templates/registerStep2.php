<?php
/**
 * @var string $login
 * @var string $password
 */
?>
<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Ваши данные для <a href=":link"><font color="#ead255">входа</font></a> в личный кабинет', array(':link' => $this->createAbsoluteUrl('/login/default/index'))) ?>:
<br />
<font color="#5aaee9"><?php echo Yii::t('main', 'Логин') ?></font>: <?php echo $login ?>
<br />
<font color="#5aaee9"><?php echo Yii::t('main', 'Пароль') ?></font>: <?php echo $password ?>
<br />