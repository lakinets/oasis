<?php
/**
 * @var string $server_name
 * @var string $login
 * @var string $password
 * @var string $referer
 */
?>
<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Ваши регистрационные данные') ?>:
<br />
<font color="#5aaee9"><?php echo Yii::t('main', 'Сервер') ?></font>: <font color="#ffffff"><?php echo $server_name ?><font>
<br />
<font color="#5aaee9"><?php echo Yii::t('main', 'Логин') ?></font>: <font color="#ffffff"><?php echo $login ?></font>
<br />
<font color="#5aaee9"><?php echo Yii::t('main', 'Пароль') ?></font>: <font color="#ffffff"><?php echo $password ?></font>
<br />
<?php if(config('referral_program.allow')) { ?>
    <font color="#5aaee9"><?php echo Yii::t('main', 'Реферальный код') ?></font>: <font color="#ffffff"><?php echo $referer ?></font>
    <br />
<?php } ?>