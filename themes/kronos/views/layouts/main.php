<?php
/**
 * Kronos → Yii2 layout
 * @var yii\web\View $this
 * @var string $content
 */
use yii\helpers\Url;
use yii\helpers\Html;

$assetsUrl = '/themes/kronos/assets';

// Помощник для пунктов меню: первая буква — в <span> для стилизации.
$splitFirstLetter = static function (string $label): string {
    $first = mb_substr($label, 0, 1);
    $rest  = mb_substr($label, 1);
    return '<span>' . Html::encode($first) . '</span>' . Html::encode($rest);
};

$this->beginPage();
?><!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title ?? 'Kronos Portal') ?></title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/style.css">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<div id="body_top">
    <div id="body_bottom">
        <div id="wrapper">

            <!-- статус серверов -->
            <?= \app\widgets\ServerStatus\ServerStatusWidget::widget(['compact' => true]) ?>

            <!-- переключатель языка (флаги) — верхний правый угол -->
            <div class="lang-switch-top">
                <?= \app\widgets\LanguageSwitcher\LanguageSwitcher::widget([
                    'containerClass' => 'language-switcher',
                    'activeClass'    => 'active',
                    'showCurrent'    => true,
                    'useFlags'       => true,
                    'flagWidth'      => 24,
                    'flagHeight'     => 16,
                    'flagLinkStyle'  => 'display:inline-block; line-height:1; margin-left: 5px;',
                    'flagImgStyle'   => 'display:block; border-radius:2px; box-shadow: 0 0 2px rgba(0,0,0,0.8);',
                ]) ?>
            </div>

            <!-- логотип -->
            <div id="header">
                <a href="<?= Url::home() ?>" class="logo-link">
                    <img src="<?= $assetsUrl ?>/images/logo.png" alt="Logo">
                </a>
            </div>

            <!-- кнопки -->
            <div id="buttons">
                <a href="<?= Url::to(['/page/patch']) ?>" id="reg_button"><?= Yii::t('main', 'Скачать патч') ?></a>

                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['/page/start']) ?>" id="start_button"><?= Yii::t('main', 'БЫСТРЫЙ') ?><br><?= Yii::t('main', 'СТАРТ') ?></a>
                <?php else: ?>
                    <a href="<?= Url::to(['/cabinet/characters']) ?>" id="start_button"><?= Yii::t('main', 'Личный') ?><br><?= Yii::t('main', 'кабинет') ?></a>
                <?php endif; ?>

                <a href="<?= Url::to(['/page/client']) ?>" id="load_button"><?= Yii::t('main', 'Скачать Клиент') ?></a>
            </div>

            <!-- меню -->
            <div id="menu">
                <a href="<?= Url::home() ?>"><?= $splitFirstLetter(Yii::t('main', 'ГЛАВНАЯ')) ?></a>
                <a href="<?= Url::to(['/page/faq']) ?>"><span>F</span>AQ</a>
                <a href="<?= Url::to(['/page/download']) ?>"><?= $splitFirstLetter(Yii::t('main', 'ЗАГРУЗКИ')) ?></a>

                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['/register']) ?>"><?= $splitFirstLetter(Yii::t('main', 'РЕГИСТРАЦИЯ')) ?></a>
                <?php else: ?>
                    <a href="<?= Url::to(['/cabinet/deposit']) ?>"><?= $splitFirstLetter(Yii::t('main', 'ПОПОЛНЕНИЕ')) ?></a>
                <?php endif; ?>

                <!-- ПОДДЕРЖКА (внешний форум) -->
                <a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/" target="_blank" rel="noopener"><?= $splitFirstLetter(Yii::t('main', 'ПОДДЕРЖКА')) ?></a>
                <a href="<?= Url::to(['/stats']) ?>"><?= $splitFirstLetter(Yii::t('main', 'СТАТИСТИКА')) ?></a>
                <a href="<?= Url::to(['/page/about']) ?>"><?= $splitFirstLetter(Yii::t('main', 'О СЕРВЕРЕ')) ?></a>
            </div>

            <!-- контент -->
            <div id="content">
                <div id="content_bottom">

                    <!-- левая колонка -->
                    <div id="left_block">

                        <!-- форма входа -->
                        <?php if (Yii::$app->user->isGuest): ?>
                            <form action="<?= Url::to(['/login']) ?>" method="post" id="login-block-form">
                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                <a href="<?= Url::to(['/login']) ?>" id="donate_button"><?= Yii::t('main', 'Вход в кабинет') ?></a>
                            </form>
                            <div id="login_links">
                                <a href="<?= Url::to(['/register']) ?>"><?= Yii::t('main', 'Регистрация') ?></a><br>
                                <a href="<?= Url::to(['/request-password-reset']) ?>"><?= Yii::t('main', 'Забыли пароль?') ?></a>
                            </div>
                        <?php else: ?>
                            <?= Yii::t('main', 'Добро пожаловать, {username}!', ['username' => Html::encode(Yii::$app->user->identity->login)]) ?>
                            <a href="<?= Url::to(['/logout']) ?>" data-method="post"><?= Yii::t('main', 'Выйти') ?></a>
                        <?php endif; ?>

                        <!-- territory -->
                        <div id="territory_block">
                            <?= \app\widgets\RaceStat\RaceStatWidget::widget() ?>
                        </div>

                        <!-- форум -->
                        <div id="forum_block">
                            <div class="theme">
                                <div class="theme_title"><a href="#"><?= Yii::t('main', 'Ретактировать в main.php 129-146 строка.') ?></a></div>
                                <div class="theme_info"><?= Yii::t('main', 'написал') ?> <a href="#">Admin</a>, в 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#"><?= Yii::t('main', 'Ну или подключить сюда флрум.') ?></a></div>
                                <div class="theme_info"><?= Yii::t('main', 'написал') ?> <a href="#">User</a>, v 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#"><?= Yii::t('main', 'Оружие и доспехи') ?></a></div>
                                <div class="theme_info"><?= Yii::t('main', 'написал') ?> <a href="#">User</a>, v 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#"><?= Yii::t('main', 'Торговля предметами') ?></a></div>
                                <div class="theme_info"><?= Yii::t('main', 'написал') ?> <a href="#">User</a>, v 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#"><?= Yii::t('main', 'Боевая экипировка') ?></a></div>
                                <div class="theme_info"><?= Yii::t('main', 'написал') ?> <a href="#">User</a>, v 15:56</div>
                            </div>
                        </div>
                    </div><!-- /left_block -->

                    <!-- правая колонка – новости + $content -->
                    <div id="right_block">
                        <div id="page_title"><span><?= Yii::t('main', 'ИНФОРМАЦИЯ') ?></span></div>

                        <!-- основной контент страницы -->
                        <?= $content ?>
                    </div><!-- /right_block -->
                </div>
            </div>

            <!-- футер -->
            <div id="footer">
                <a href="https://github.com/lakinets/oasis" title="<?= Yii::t('main', 'Скачать Oasis') ?>" id="dkarts" target="_blank" rel="noopener"></a>
                &copy; Copyright 2025 <a class="copy" href="https://oasis.gamer.gd">oasis.gamer.gd</a>
            </div>

        </div><!-- /wrapper -->
    </div><!-- /body_bottom -->
</div><!-- /body_top -->

<!-- Скрипт убирает эффект подпрыгивания (сохранение скролла при внутренних переходах) -->
<script>
(function() {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const CONFIG = {
        containerSelector: '#content',
        linkSelector: 'a[href]:not([href^="#"]):not([target])'
    };

    document.addEventListener('click', function(e) {
        const a = e.target.closest(CONFIG.linkSelector);
        if (!a || a.hostname !== location.hostname) return;

        // Не перехватываем ссылки переключателя языка — пусть работают как обычные переходы
        if (a.closest('.lang-switch-top')) return;

        e.preventDefault();
        loadPage(a.href, true);
    });

    function loadPage(url, pushState) {
        const container = document.querySelector(CONFIG.containerSelector);
        if (!container) {
            location.href = url;
            return;
        }

        const scrollY = window.pageYOffset || document.documentElement.scrollTop;
        document.body.style.minHeight = document.documentElement.scrollHeight + 'px';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                const newContent = newDoc.querySelector(CONFIG.containerSelector);
                const newTitle = newDoc.querySelector('title');

                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
                    if (newTitle) document.title = newTitle.innerText;

                    if (pushState) {
                        history.pushState({ scrollY: scrollY }, '', url);
                    }

                    window.scrollTo(0, scrollY);

                    requestAnimationFrame(() => {
                        window.scrollTo(0, scrollY);
                        document.body.style.minHeight = '';
                    });
                } else {
                    location.href = url;
                }
            })
            .catch(() => {
                document.body.style.minHeight = '';
                location.href = url;
            });
    }

    window.addEventListener('popstate', function(e) {
        if (e.state && typeof e.state.scrollY !== 'undefined') {
            loadPage(location.href, false);
        } else {
            location.reload();
        }
    });
})();
</script>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>