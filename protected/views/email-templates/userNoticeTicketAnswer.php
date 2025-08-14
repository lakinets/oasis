<?php
/**
 * @var Users $user
 * @var Tickets $ticket
 */
?>
<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Был дан ответ на Ваш тикет') ?>
<br />
<a href="<?php echo app()->createAbsoluteUrl('/cabinet/tickets/view', array('ticket_id' => $ticket->getPrimaryKey())) ?>"><?php echo Yii::t('main', 'Перейти к просмотру') ?></a><br>
<?php echo Yii::t('main', 'Ссылка на тикет') ?>:<br>
<?php echo app()->createAbsoluteUrl('/cabinet/tickets/view', array('ticket_id' => $ticket->getPrimaryKey())) ?>