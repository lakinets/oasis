<?php
use yii\helpers\Html;

/** @var \app\models\Pages|null $model */
/** @var string $page */
/** @var bool $notFound */

// SEO
if (!$notFound && $model) {
    $this->title = $model->seo_title ?: $model->title;
    $this->registerMetaTag(['name' => 'keywords',    'content' => $model->seo_keywords]);
    $this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description]);
}
?>
<div class="page-view">
    <?php if ($notFound): ?>
        <div class="alert alert-danger">
            Страница "<?= Html::encode($page) ?>" не существует.
        </div>
    <?php else: ?>
        <h1><?= Html::encode($model->title) ?></h1>
        <div class="content">
            <?= $model->text ?>
        </div>
    <?php endif; ?>
</div>