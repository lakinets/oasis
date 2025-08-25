<?php
use yii\helpers\Html;
$this->title = 'Оплата не выполнена';
?>
<h1>Оплата не выполнена</h1>
<p>Провайдер: <b><?= Html::encode($provider) ?></b></p>
<p>Заказ: <b><?= Html::encode($orderId) ?></b></p>
<?php if (!empty($msg)): ?>
    <p>Причина: <?= Html::encode($msg) ?></p>
<?php endif; ?>
