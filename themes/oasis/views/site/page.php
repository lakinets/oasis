<?php
use yii\helpers\Html;

/** @var \app\models\Pages|null $model */
/** @var \app\models\PageTranslation|null $translation */
/** @var string $page */
/** @var bool $notFound */

if ($notFound || !$model) {
    $this->title = Yii::t('main', 'Страница не найдена');
    ?>
    <div class="page-view">
        <div class="alert alert-danger">
            <?= Yii::t('main', 'Страница "{page}" не существует.', ['page' => Html::encode($page)]) ?>
        </div>
    </div>
    <?php
    return;
}

$seoTitle       = $translation->seo_title       ?? $model->seo_title       ?? null;
$seoKeywords    = $translation->seo_keywords    ?? $model->seo_keywords    ?? null;
$seoDescription = $translation->seo_description ?? $model->seo_description ?? null;
$fallbackTitle  = $translation->title           ?? $model->title;

$this->title = $seoTitle ?: $fallbackTitle;

if (!empty($seoKeywords)) {
    $this->registerMetaTag(['name' => 'keywords', 'content' => $seoKeywords]);
}
if (!empty($seoDescription)) {
    $this->registerMetaTag(['name' => 'description', 'content' => $seoDescription]);
}

$content = $translation->text ?? $model->text;
?>
<div class="page-view">
    <div class="content">
        <?= $content ?>
    </div>
</div>