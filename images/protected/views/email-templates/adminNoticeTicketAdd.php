<?php
/**
 * @var Users $user
 * @var Tickets $ticket
 */
?>
<font color="#ead255" face="Trebuchet MS" style="font-size: 24px;"><?php echo Yii::t('main', 'Здравствуйте!') ?></font>
<br /><br /><br /><br />
<?php echo Yii::t('main', 'Был создан новый тикет') ?>
<br />
Пользователь: <a href="<?php echo app()->createAbsoluteUrl('/backend/users/view', array('user_id' => $user->getPrimaryKey())) ?>" title="<?php echo Yii::t('main', 'Перейти к просмотру пользователя') ?>"><?php echo CHtml::encode($user->login) ?></a><br>
Тикет: <a href="<?php echo app()->createAbsoluteUrl('/backend/tickets/edit', array('id' => $ticket->getPrimaryKey())) ?>" title="<?php echo Yii::t('main', 'Перейти к просмотру тикета') ?>"><?php echo CHtml::encode($ticket->title) ?></a>
