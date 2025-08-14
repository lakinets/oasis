<?php
use yii\helpers\Html;

/** @var $exception Exception */

$this->title = 'Ошибка';
?>

<h1><?= Html::encode($this->title) ?></h1>

<p><?= Html::encode($exception->getMessage()) ?></p>
