<?php
$this->title = 'Оплата';
?>
<h1>Оплата транзакции #<?= $model->id ?></h1>
<p>Сумма: <?= $model->sum ?></p>
<p>Метод оплаты: <?= $model->payment_system ?></p>