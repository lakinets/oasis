<?php
/**
 * @var array $languages
 */

// Удаляем из списка языков текущий язык
//if(is_array($languages) && isset($languages[app()->getLanguage()]))
//{
//    unset($languages[app()->getLanguage()]);
//}

$assetsUrl = assetsUrl('widgets.Languages.assets');

js('//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', CClientScript::POS_END);
js($assetsUrl . '/js/main.js', CClientScript::POS_END);
css($assetsUrl . '/css/style.css');
?>

<?php if($languages) { ?>

    <script>
        var languages = <?php echo json_encode(array_keys($languages)) ?>;
    </script>

    <div class="languages">
        <ul>
            <?php foreach($languages as $code => $lang) { ?>
                <li>
                    <a data-lang="<?php echo $code ?>" title="<?php echo $lang ?>"<?php echo ($code == app()->getLanguage() ? ' class="active"' : '') ?>>
                        <img src="<?php echo $assetsUrl ?>/images/<?php echo $code ?>.png" alt=""/>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>

<?php } ?>