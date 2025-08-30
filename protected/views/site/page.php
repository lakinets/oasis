<?php
use yii\helpers\Html;

/** @var \app\models\Pages|null $model */
/** @var string $page */
/** @var bool $notFound */

// SEO
if (!$notFound && $model) {
    $this->title = $model->seo_title ?: $model->title;
    if (!empty($model->seo_keywords)) {
        $this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords]);
    }
    if (!empty($model->seo_description)) {
        $this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description]);
    }
} else {
    $this->title = 'Страница не найдена';
}
?>
<div class="page-view">
    <?php if ($notFound): ?>
        <div class="alert alert-danger">
            Страница "<?= Html::encode($page) ?>" не существует.
        </div>
    <?php else: ?>
        <div class="content">
            <?= $model->text /* тут правильное поле */ ?>
        </div>
    <?php endif; ?>
</div>
