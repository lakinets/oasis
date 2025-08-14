<?php
/**
 * @var CActiveDataProvider $dataProvider
 * @var News[] $data
 */

$this->pageTitle = Yii::t('main', 'Новости');
?>

<?php if($data = $dataProvider->getData()) { ?>

    <div class="entry news">
        <div class="scroll-pane">
            <?php foreach($data as $row) { ?>
                <div style="margin-bottom: 30px;">
                    <header>
                        <h1><?php echo CHtml::link($row->title, array('/news/default/detail', 'news_id' => $row->id)) ?></h1>
                    </header>
                    <div class="desc">
                        <?php if($row->imgIsExists()) { ?>
                            <img src="<?php echo $row->getImgUrl() ?>" alt="" style="margin: 0 20px 20px 0; float: left;"/>
                        <?php } ?>
                        <?php echo $row->description ?>
                    </div>
                    <div class="clearfix"></div>
                    <footer>
                        <ul>
                            <li><?php echo Yii::t('main', 'Дата') ?>: <span class="label label-info"><?php echo $row->getDate() ?></span></li>
                        </ul>
                    </footer>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="news">
        <?php $this->widget('CLinkPager', array(
            'pages' => $dataProvider->getPagination(),
        )) ?>
    </div>
<?php } else { ?>
    <?php echo Yii::t('main', 'Нет данных.') ?>
<?php } ?>