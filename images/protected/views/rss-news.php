<?php
/**
 * @var CArrayDataProvider $dataProvider
 */

$this->pageTitle = Yii::t('main', 'Новости с форума');
?>

<?php if(!is_string($dataProvider)) { ?>

    <div class="entry">
        <div class="scroll-pane">
            <?php foreach($dataProvider->getData() as $row) { ?>
                <div>
                    <header>
                        <h1><?php echo CHtml::link($row['title'], $row['link']) ?></h1>
                    </header>
                    <?php if(isset($row['description'])) { ?>
                        <div class="desc"><?php echo $row['description'] ?></div>
                    <?php } ?>
                    <footer>
                        <ul>
                            <li><?php echo Yii::t('main', 'Дата') ?>: <span class="label label-info"><?php echo $row['date'] ?></span></li>
                            <li><?php echo CHtml::link(Yii::t('main', 'Подробнее'), $row['link']) ?></li>
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
    <div class="alert alert-info">
        <?php echo $dataProvider ?>
    </div>
<?php } ?>