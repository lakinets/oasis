<?php
/**
 * @var array $content
 * @var int $totalOnline
 */

$maxLimitOnline = 500; // Верхний предел онлайна (чем он ниже тем больше будет закрашена полоска)
?>

<?php if(config('server_status.allow')) { ?>
    <?php if($content) { ?>
        <?php
        // Подключаю библиотеки для работы с графиком онлайна (See: https://github.com/pguso/jquery-plugin-circliful)
        css(assetsUrl() . '/js/libs/circliful/css/jquery.circliful.css');
        js(assetsUrl() . '/js/libs/circliful/js/jquery.circliful.min.js', CClientScript::POS_END);

        clientScript()->registerScript('circliful', '
            $(function(){
                $(".circuit").circliful();
            });
        ', CClientScript::POS_END);
        ?>
        <table>
            <tr>
                <?php foreach($content as $gsId => $row) { ?>
                    <?php if(isset($row['error'])) { ?>
                        <td><?php echo $row['error'] ?></td>
                    <?php } else { ?>
                        <?php
                        /**
                         * Кол-во игроков: $row['online']
                         * Название сервера: $row['gs']->name
                         * Ссылка на статистику сервера: url('/stats/default/index', array('gs_id' => $row['gs']->id))
                         */
                        ?>
                        <td>
                            <div class="circuit"
                                 data-dimension="130"
                                 data-info="<?php echo $row['online'] ?>"
                                 data-width="10"
                                 data-text="<?php echo $row['gs']->name ?>"
                                 data-fgcolor="#CEB004"
                                 data-bgcolor="#251510"
                                 data-fill="#AD3E23"
                                 data-total="<?php echo $maxLimitOnline ?>"
                                 data-part="<?php echo $row['online'] ?>"
                                ></div>
                        </td>
                    <?php } ?>
                <?php } ?>
            </tr>
        </table>
    <?php } else { ?>
        <?php echo Yii::t('main', 'Нет данных.') ?>
    <?php } ?>
<?php } else { ?>
    <?php echo Yii::t('main', 'Модуль отключен.') ?>
<?php } ?>