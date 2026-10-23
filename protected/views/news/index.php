<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('main', 'Новости');

$models = $dataProvider->getModels();
$isEn = (Yii::$app->language === 'en');
?>

<?php if (!empty($models)): ?>

    <div class="entry news">
        <div class="scroll-pane">
            <?php foreach ($models as $model): ?>
                <?php 
                    // Проверяем язык и берем английский перевод, если он есть
                    $title = ($isEn && !empty($model->title_en)) ? $model->title_en : $model->title;
                    $description = ($isEn && !empty($model->description_en)) ? $model->description_en : $model->description;
                ?>
                <div style="margin-bottom: 30px;">
                    <header>
                        <h1><?= Html::a(Html::encode($title), ['view', 'id' => $model->id]) ?></h1>
                    </header>
                    <div class="desc">
                        <?= $description ?>
                    </div>
                    <div class="clearfix"></div>
                    <footer>
                        <ul>
                            <li>
                                <?= Yii::t('main', 'Дата') ?>: 
                                <span class="label label-info">
                                    <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y') ?>
                                </span>
                            </li>
                        </ul>
                    </footer>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="news">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination']
        ]) ?>
    </div>

<?php else: ?>
    <div class="alert alert-info">
        <?= Yii::t('main', 'Нет данных.') ?>
    </div>
<?php endif; ?>