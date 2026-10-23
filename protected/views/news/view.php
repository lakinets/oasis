<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$isEn = (Yii::$app->language === 'en');

// Берем нужный язык с откатом на русский
$title = ($isEn && !empty($model->title_en)) ? $model->title_en : $model->title;
$text  = ($isEn && !empty($model->text_en)) ? $model->text_en : $model->text;

$this->title = $title;
?>

<div class="entry news" style="margin-bottom: 50px;">
    <header class="mb-3">
        <h1><?= Html::encode($title) ?></h1>
        <div class="text-muted small">
            <?= Yii::t('main', 'Дата') ?>: 
            <span class="label label-info">
                <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y H:i') ?>
            </span>
        </div>
    </header>

    <hr>

    <div class="content">
        <?= $text ?>
    </div>

    <hr>
    
    <div class="mt-4">
        <?= Html::a('← ' . Yii::t('main', 'Назад к новостям'), ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>