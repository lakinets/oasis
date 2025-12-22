<?php
use yii\helpers\Html;

/** @var $exception \Exception */

$this->title = 'Ошибка';
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= nl2br(Html::encode($exception->getMessage())) ?></p>
</div>
