<?php
/**
 * @var yii\web\View $this
 * @var string $content
 */
$assetsUrl = Yii::getAlias('@web/themes/forest/assets');
?><!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($this->title ?? 'Добро пожаловать.') ?></title>
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
                <li><a href="/">Главная</a></li>
                <li class="menu-auth">
				<?php if (Yii::$app->user->isGuest): ?>
					<a href="/login">Вход</a>
				<?php else: ?>
					<a href="/logout" data-method="post">Выход</a>
				<?php endif; ?>
				</li>
                <li><a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/">Поддержка</a></li>
                <li><a href="/stats">Статус</a></li>
                <li><a href="/register">Регистрация</a></li>
            </ul>
        </div>
        <div class="logo">
            <a href="/"><img src="<?= $assetsUrl ?>/images/logo.png" alt="Logo"></a>
        </div>
        <div class="reg-buttons">
            <div class="download-button but">
                <a href="/cabinet/characters">Кабинет</a><br>Управление и покупки
            </div>
            <div class="start-button but">
                <a href="/page/start">Быстрый старт</a>
            </div>
            <div class="lang-button but">
                Скачать<br>
                <a href="/page/client">Клиент</a> / <a href="/page/path">Патч</a>
            </div>
        </div>
        <!-- СЛАЙДЕР -->
        <div class="slider">
            <div class="slider-in">
                <div id="owl-demo" class="owl-carousel">
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent1"><img src="<?= $assetsUrl ?>/images/img1.jpg" alt=""></a></div>
                        Новое событие<br><span>Не пропусти!</span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent2"><img src="<?= $assetsUrl ?>/images/img2.jpg" alt=""></a></div>
                        Новое событие<br><span>Не пропусти!</span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent3"><img src="<?= $assetsUrl ?>/images/img3.jpg" alt=""></a></div>
                        Новое событие<br><span>Не пропусти!</span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent4"><img src="<?= $assetsUrl ?>/images/img1.jpg" alt=""></a></div>
                        Новое событие<br><span>Не пропусти!</span>
                    </div>
                    <div class="item">
                        <div class="cat-item"><a href="/page/ivent5"><img src="<?= $assetsUrl ?>/images/img2.jpg" alt=""></a></div>
                        Новое событие<br><span>Не пропусти!</span>
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
                    <div class="panel-heading">Статус серверов</div>
                    <img src="<?= $assetsUrl ?>/images/status-title.png" alt="">
                </div>
                <div class="panel panel-default server-status">
                    <div class="panel-body">
                        <strong>Test server</strong><br>
                        Game: offline<br>
                        Login: offline<br>
                        Online: 0
                        <hr>
                        <p><strong>Всего онлайн: 0</strong></p>
                    </div>
                </div>
            </div>
            <div class="block-forum">
                <div class="forum-title"><img src="<?= $assetsUrl ?>/images/forum-img.png" alt="Forum"></div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="Новость1">Тут будет какая то ссылка</a></div>
                    <div class="forums-autor">Автор: <a href=""><span class="at">Админ</span></a>, 2 Января at <span>19:30</span></div>
                </div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="Новость2">Тут будет какая то ссылка</a></div>
                    <div class="forums-autor">Автор: <a href=""><span class="at">Админ</span></a>, 2 Января at <span>19:30</span></div>
                </div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="Новость3">Редактируй это в main.php</a></div>
                    <div class="forums-autor">Автор: <a href=""><span class="at">Админ</span></a>, 2 Января at <span>19:30</span></div>
                </div>
                <div class="forums">
                    <div class="forums-name"><a href="/page/topic1" title="Новость4">Добавляй и удаляй пункты</a></div>
                    <div class="forums-autor">Автор: <a href=""><span class="at">Админ</span></a>, 2 Января at <span>19:30</span></div>
                </div>
            </div>
        </div>

        <div class="welcome"><h1>Добро пожаловать.</h1></div>

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
                <li><a href="/">Главная</a></li>
                <li class="menu-auth">
				<?php if (Yii::$app->user->isGuest): ?>
					<a href="/login">Вход</a>
				<?php else: ?>
					<a href="/logout" data-method="post">Выход</a>
				<?php endif; ?>
				</li>
                <li><a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/">Поддержка</a></li>
                <li><a href="/stats">Статус</a></li>
                <li><a href="/register">Регистрация</a></li>
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

<!-- Ваш скрипт сохранения скролла + fetch-роутер без прыжков -->
<script>
/* === 1. Сохраняем / восстанавливаем скролл (ваш оригинал) === */
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
        e.preventDefault();

        fetch(a.href, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(r => r.ok ? r.text() : Promise.reject(r))
            .then(html => {
                const start = html.indexOf('<main class="content-bg">');
                const end   = html.indexOf('</main>', start) + 7;
                if (start === -1 || end === 6) throw 'no <main>';
                document.querySelector('main.content-bg').outerHTML = html.slice(start, end);
                history.pushState(null, null, a.href);
                document.title = (/<title>(.+?)<\/title>/.exec(html) || [,document.title])[1];
            })
            .catch(() => location.href = a.href);
    });
    window.addEventListener('popstate', () => location.reload());
})();
</script>

</body>
</html>