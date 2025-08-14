<?php
/**
 * @var News $model
 */

$this->pageTitle = $model->title
?>

<h2 class="title"><?php echo e($model->title) ?></h2>

<div class="entry news">
    <div class="scroll-pane">
        <div class="desc">
            <?php if($model->imgIsExists()) { ?>
                <img src="<?php echo $model->getImgUrl() ?>" alt="" style="margin: 0 20px 20px 0; float: left;"/>
            <?php } ?>
            <?php echo $model->text ?>
        </div>
        <div class="clearfix"></div>
        <?php if(config('news.detail.socials') == 1) { ?>
            <?php $this->widget('app.widgets.NewsSocials.NewsSocials', array(
                'params' => array(
                    'data-url'   => $this->createAbsoluteUrl('/news/default/detail', array('news_id' => $model->id)),
                    'data-title' => e($model->title),
                    'data-image' => $model->imgIsExists() ? $model->getImgUrl() : '',
                    'data-description' => $model->description,
                )
            )) ?>
        <?php } ?>

        <?php echo CHtml::link(Yii::t('main', 'Назад'), array('/news/default/index')) ?>
    </div>
</div>