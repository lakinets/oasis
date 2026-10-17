<?php
/**
 * @var yii\web\View $this
 * @var string $content
 */
$assetsUrl = Yii::getAlias('@web/themes/forest/assets');
?><!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($this->title ?? Yii::t('main', 'Добро пожаловать.')) ?></title>
    <link rel="stylesheet" href="<?= $assetsUrl ?>/css/style.css">
    <link rel="stylesheet" href="<?= $assetsUrl ?>/owl/owl.carousel.css">
    <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="wrapper">

    <!-- HEADER -->
    <header class="header">
        <div class="menu-top">
            <ul>
                <li><a href="/"><?= Yii::t('main', 'Главная') ?></a></li>
                <li class="menu-auth">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="/login"><?= Yii::t('main', 'Вход') ?></a>
                <?php else: ?>
                    <a href="/logout" data-method="post"><?= Yii::t('main', 'Выход') ?></a>
                <?php endif; ?>
                </li>
                <li><a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/"><?= Yii::t('main', 'Поддержка') ?></a></li>
                <li><a href="/stats"><?= Yii::t('main', 'Статус') ?></a></li>
                <li><a href="/register"><?= Yii::t('main', 'Регистрация') ?></a></li>
            </ul>
        </div>

        <!-- Переключатель языка (флаги) — отдельным блоком поверх меню -->
        <div class="lang-switch">
            <?= \app\widgets\LanguageSwitcher\LanguageSwitcher::widget([
                'containerClass' => 'language-switcher',
                'activeClass'    => 'active',
                'showCurrent'    => true,
                'useFlags'       => true,
                'flagWidth'      => 30,
                'flagHeight'     => 20,
                'flagLinkStyle'  => 'display:inline-block; line-height:1;',
                'flagImgStyle'   => 'display:block; border-radius:2px;',
            ]) ?>
        </div>

        <div class="logo">
            <a href="/"><img src="<?= $assetsUrl ?>/images/logo.png" alt="Logo"></a>
        </div>
        <div class="reg-buttons">
            <div class="download-button but">
                <a href="/cabinet/characters"><?= Yii::t('main', 'Кабинет') ?></a><br><?= Yii::t('main', 'Управление и покупки') ?>
            </div>
            <div class="start-button but">
                <a href="/page/start"><?= Yii::t('main', 'Быстрый старт') ?></a>
            </div>
            <div class="lang-button but">
                <?= Yii::t('main', 'Скачать') ?><br>
                <a href="/page/client"><?= Yii::t('main', 'Клиент') ?></a> / <a href="/page/path"><?= Yii::t('main', 'Патч') ?></a>
            </div>
        </div>
        <!-- СЛАЙДЕР -->
        <div class="slider">
            <div class="slider-in">
                <div id="owl-demo" class="owl-carousel">
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent1"><img src="<?= $assetsUrl ?>/images/img1.jpg" alt=""></a></div>
                        <?= Yii::t('main', 'Новое событие') ?><br><span><?= Yii::t('main', 'Не пропусти!') ?></span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent2"><img src="<?= $assetsUrl ?>/images/img2.jpg" alt=""></a></div>
                        <?= Yii::t('main', 'Новое событие') ?><br><span><?= Yii::t('main', 'Не пропусти!') ?></span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent3"><img src="<?= $assetsUrl ?>/images/img3.jpg" alt=""></a></div>
                        <?= Yii::t('main', 'Новое событие') ?><br><span><?= Yii::t('main', 'Не пропусти!') ?></span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent4"><img src="<?= $assetsUrl ?>/images/img1.jpg" alt=""></a></div>
                        <?= Yii::t('main', 'Новое событие') ?><br><span><?= Yii::t('main', 'Не пропусти!') ?></span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent5"><img src="<?= $assetsUrl ?>/images/img2.jpg" alt=""></a></div>
                        <?= Yii::t('main', 'Новое событие') ?><br><span><?= Yii::t('main', 'Не пропусти!') ?></span>
                    </div>
                </div>
                <div class="customNavigation">
                    <a class="btn prev"><img src="<?= $assetsUrl ?>/images/prev-icon.png" alt=""></a>
                    <a class="btn next"><img src="<?= $assetsUrl ?>/images/next-icon.png" alt=""></a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN -->
    <main class="content-bg">
        <div class="right-sidebar">
            <div class="server-status">
                <div class="server-status-title">
                    <div class="panel-heading"><?= Yii::t('main', 'Статус серверов') ?></div>
                    <img src="<?= $assetsUrl ?>/images/status-title.png" alt="">
                </div>
                <div class="panel panel-default server-status">
                    <div class="panel-body">
                        <strong>Test server</strong><br>
                        Game: offline<br>
                        Login: offline<br>
                        Online: 0
                        <hr>
                        <p><strong><?= Yii::t('main', 'Всего онлайн: 0') ?></strong></p>
                    </div>
                </div>
            </div>
            <div class="block-forum">
                <div class="forum-title"><img src="<?= $assetsUrl ?>/images/forum-img.png" alt="Forum"></div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="<?= Yii::t('main', 'Новость1') ?>"><?= Yii::t('main', 'Тут будет какая то ссылка') ?></a></div>
                    <div class="forums-autor"><?= Yii::t('main', 'Автор:') ?> <a href=""><span class="at"><?= Yii::t('main', 'Админ') ?></span></a>, 2 <?= Yii::t('main', 'Января') ?> at <span>19:30</span></div>
                </div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="<?= Yii::t('main', 'Новость2') ?>"><?= Yii::t('main', 'Тут будет какая то ссылка') ?></a></div>
                    <div class="forums-autor"><?= Yii::t('main', 'Автор:') ?> <a href=""><span class="at"><?= Yii::t('main', 'Админ') ?></span></a>, 2 <?= Yii::t('main', 'Января') ?> at <span>19:30</span></div>
                </div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="<?= Yii::t('main', 'Новость3') ?>"><?= Yii::t('main', 'Редактируй это в main.php') ?></a></div>
                    <div class="forums-autor"><?= Yii::t('main', 'Автор:') ?> <a href=""><span class="at"><?= Yii::t('main', 'Админ') ?></span></a>, 2 <?= Yii::t('main', 'Января') ?> at <span>19:30</span></div>
                </div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="<?= Yii::t('main', 'Новость4') ?>"><?= Yii::t('main', 'Добавляй и удаляй пункты') ?></a></div>
                    <div class="forums-autor"><?= Yii::t('main', 'Автор:') ?> <a href=""><span class="at"><?= Yii::t('main', 'Админ') ?></span></a>, 2 <?= Yii::t('main', 'Января') ?> at <span>19:30</span></div>
                </div>
            </div>
        </div>

        <div class="welcome"><h1><?= Yii::t('main', 'Добро пожаловать.') ?></h1></div>

        <div class="content-offset" style="margin-left:62px;">
            <div class="content">
                <?= $content ?>
                <div class="page-view"></div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-menu">
            <ul>
                <li><a href="/"><?= Yii::t('main', 'Главная') ?></a></li>
                <li class="menu-auth">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="/login"><?= Yii::t('main', 'Вход') ?></a>
                <?php else: ?>
                    <a href="/logout" data-method="post"><?= Yii::t('main', 'Выход') ?></a>
                <?php endif; ?>
                </li>
                <li><a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/"><?= Yii::t('main', 'Поддержка') ?></a></li>
                <li><a href="/stats"><?= Yii::t('main', 'Статус') ?></a></li>
                <li><a href="/register"><?= Yii::t('main', 'Регистрация') ?></a></li>
            </ul>
        </div>
        <div class="copy">
            &copy; Copyright 2025 <a class="copy" href="https://oasis.gamer.gd">oasis.gamer.gd</a>
        </div>
    </footer>
</div><!-- .wrapper -->

<script src="<?= $assetsUrl ?>/js/jquery-1.9.1.min.js"></script>
<script src="<?= $assetsUrl ?>/owl/owl.carousel.js"></script>
<script>
$(function () {
    $("#owl-demo").owlCarousel({
        items: 3,
        itemsDesktop: [1000, 3],
        itemsDesktopSmall: [900, 3],
        itemsTablet: [600, 1]
    });
    $(".next").on("click", function () {
        $("#owl-demo").trigger("owl.next");
    });
    $(".prev").on("click", function () {
        $("#owl-demo").trigger("owl.prev");
    });
});
</script>

<!-- Скрипт сохранения скролла + fetch-роутер без прыжков -->
<script>
/* === 1. Сохраняем / восстанавливаем скролл === */
(function () {
    const key = 'scroll:' + location.pathname + location.search;
    window.addEventListener('DOMContentLoaded', function () {
        const y = sessionStorage.getItem(key);
        if (y !== null) {
            const pos = parseInt(y, 10);
            if (!isNaN(pos)) window.scrollTo(0, pos);
        }
    });
    window.addEventListener('beforeunload', function () {
        sessionStorage.setItem(key, String(window.pageYOffset || document.documentElement.scrollTop || 0));
    });
})();

/* === 2. Fetch-роутер: подгружаем <main> без прыжка === */
(function () {
    document.addEventListener('click', function (e) {
        const a = e.target.closest('a[href]:not([href^="#"]):not([target])');
        if (!a || a.hostname !== location.hostname) return;

        // Ссылки переключателя языка не перехватываем —
        // пусть браузер сам обработает Set-Cookie и редирект.
        if (a.closest('.lang-switch')) return;

        e.preventDefault();

        fetch(a.href, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(r => r.ok ? r.text() : Promise.reject(r))
            .then(html => {
                const start = html.indexOf('<main class="content-bg">');
                const end   = html.indexOf('</main>', start) + 7;
                if (start === -1 || end === 6) throw 'no <main>';
                document.querySelector('main.content-bg').outerHTML = html.slice(start, end);
                history.pushState(null, null, a.href);
                document.title = (/<title>(.+?)<\/title>/.exec(html) || [, document.title])[1];
            })
            .catch(() => location.href = a.href);
    });
    window.addEventListener('popstate', () => location.reload());
})();
</script>

</body>
</html>