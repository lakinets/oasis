<?php
/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Url;

$this->beginPage();
?>
<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= isset($this->title) ? $this->title : 'Lineage 2 Website' ?></title>
    <meta name="author" content="Angfuzsoft">
    <meta name="description" content="Spiel - Esports and Gaming HTML Template">
    <meta name="keywords" content="l2 oasis, c4, l2, lineage, lineage2, lineage 2">
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= Url::base(true) ?>/themes/oasis/assets/img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= Url::base(true) ?>/themes/oasis/assets/img/favicons/apple-icon-60x60.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Url::base(true) ?>/themes/oasis/assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= Url::base(true) ?>/themes/oasis/assets/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?= Url::base(true) ?>/themes/oasis/assets/img/favicons/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/oasis/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/oasis/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/oasis/assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/oasis/assets/css/slick.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/oasis/assets/css/style.css">

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody(); ?>

<!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a>.</p>
<![endif]-->

<!-- Custom Cursor -->
<div class="vs-cursor"></div>

<!-- Preloader -->
<div class="preloader">
    <button class="vs-btn preloaderCls">Не ждать полную загрузку страницы.</button>
    <div class="preloader-inner">
        <div class="vs-loadholder">
            <div class="vs-loader">
                <span class="loader-text">
                    <img src="<?= Url::base(true) ?>/themes/oasis/assets/img/loader.png" style="height: 90px; margin-top:20px;">
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Menu -->
<div class="vs-menu-wrapper">
    <div class="vs-menu-area text-center">
        <button class="vs-menu-toggle"><i class="fal fa-times"></i></button>
        <div class="mobile-logo">
            <a href="<?= Url::home() ?>"><img src="<?= Url::base(true) ?>/themes/oasis/assets/img/logo.png" alt="Logo"></a>
        </div>
        <div class="vs-mobile-menu">
            <ul>
                <li><a href="<?= Url::home() ?>">Главная</a></li>
                <li><a href="<?= Url::to(['/stats']) ?>">Статус</a></li>
                <li><a href="<?= Url::to(['/page/onlypc']) ?>">Файлы</a></li>
                <li><a href="<?= Url::to(['/login']) ?>">Кабинет</a></li>
        </div>
    </div>
</div>

<!-- Popup Search Box -->
<div class="popup-search-box d-none d-lg-block">
    <button class="searchClose border-theme text-theme"><i class="fal fa-times"></i></button>
    <form action="#">
        <input type="text" class="border-theme" placeholder="What are you looking for">
        <button type="submit"><i class="fal fa-search"></i></button>
    </form>
</div>

<!-- Sidemenu -->
<div class="sidemenu-wrapper d-none d-lg-block">
    <div class="sidemenu-content">
        <button class="closeButton sideMenuCls"><i class="far fa-times"></i></button>
        <div class="widget">
            <div class="vs-widget-about">
                <div class="footer-logo">
                    <a href="<?= Url::home() ?>"><img src="<?= Url::base(true) ?>/themes/oasis/assets/img/logo.png" alt="Logo"></a>
                </div>
                <p class="footer-about-text">.</p>
                <div class="multi-social">
                    <a href="https://www.facebook.com/groups/1799664900688601"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<header class="vs-header header-layout3">
    <div class="container"> 
                </div>
            </div>
        </div>
    </div>
    <div class="sticky-wrapper">
        <div class="sticky-active">
            <div class="header-menu-area">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <div class="header-logo">
                            <img src="<?= Url::base(true) ?>/themes/oasis/assets/img/logo.png" alt="Logo">
                        </div>
                    </div>
                    <div class="col-auto">
                        <nav class="main-menu menu-style2 d-none d-lg-block">
                            <ul>
                                <li><a href="<?= Url::home() ?>">Главная</a></li>
                                <li><a href="<?= Url::to(['/page/files']) ?>">Файлы</a></li>
                                <?php if (Yii::$app->user->isGuest): ?>
                                    <li><a href="/register">Регистрация</a></li>
                                <?php endif; ?>
                                <li class="menu-item-has-children">
                                    <a href="#">Инфо</a>
                                    <ul class="sub-menu">
                                        <?php // \app\widgets\ServerStatus\ServerStatusWidget::widget() ?>
                                    </ul>
                                </li>
                                <li><a href="/stats">Статус</a></li>
                                <li><a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/" target="_blank" rel="noopener">Поддержка</a></li>
                            </ul>
                        </nav>
                        <button type="button" class="vs-menu-toggle d-inline-block d-lg-none"><i class="fas fa-bars"></i></button>
                    </div>

                    <!-- КНОПКА ЛК/КАБИНЕТ/ВЫХОД -->
                    <div class="col-auto d-none d-lg-block">
                        <?php
                        $path = Yii::$app->request->pathInfo; // без ведущего слеша
                        $inCabinet = strpos($path, 'cabinet') === 0;
                        if (Yii::$app->user->isGuest): ?>
                            <a href="<?= Url::to(['/login']) ?>" class="vs-btn outline-style d-none d-xl-block">
                                <i class="fab fa-expeditedssl"></i>Личный кабинет
                            </a>
                        <?php else: ?>
                            <?php if ($inCabinet): ?>
                                <a href="<?= Url::to(['/logout']) ?>" class="vs-btn outline-style d-none d-xl-block">
                                    <i class="fal fa-sign-out"></i>Выход
                                </a>
                            <?php else: ?>
                                <a href="<?= Url::to(['/cabinet']) ?>" class="vs-btn outline-style d-none d-xl-block">
                                    <i class="fal fa-user"></i>Кабинет
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <!-- /КНОПКА ЛК/КАБИНЕТ/ВЫХОД -->
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Hero Area -->
<section class="vs-hero-wrapper position-relative bg-dark">
    <div class="hero-social d-none d-lg-block">
        <a href="https://www.facebook.com/groups/1799664900688601" target="_blank"><span>Facebook</span></a>
    </div>
    <div class="vs-carousel" id="heroSlide1" data-slide-show="1" data-md-slide-show="1" data-fade="true">
        <div class="slider">
            <div class="hero-clip-slider">
                <div class="hero-clip-img" data-bg-src="<?= Url::base(true) ?>/themes/oasis/assets/img/hero/hero-1-11.jpg"></div>
                <div class="hero-clip-shape bg-theme2"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-xxl-6 offset-xl-1">
                            <div class="hero-clip-content">
                                <h1 class="hero-clip-title">GHTWEB 5 <span class="text-theme3"><br>REMASTER</br></span> <span class="text-theme2"><br>opensource for lineage 2</br></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Area -->
<section class="vs-features-wrap space-top space-extra-bottom">
    <div class="parallax" data-bg-class="bg-dark" data-parallax-image="<?= Url::base(true) ?>/themes/oasis/assets/img/shape/features-shape.png"></div>
    <div class="container">
        <div class="title-area text-center text-xl-start">
            <span class="sub-title"></span>
            <?= $content ?>
        </div>
</section>

<!-- Blog Area -->
<section class="vs-blog-wrapper space-top space-extra-bottom" data-overlay="title" data-opacity="7">
    <div class="parallax" data-bg-class="bg-title" data-parallax-image="<?= Url::base(true) ?>/themes/oasis/assets/img/bg/blog-1-11.jpg"></div>
    <div class="container z-index-common">
        <div class="title-area text-center">
            <h2 class="sec-title-style2">Пару слов о проекте</h2>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-xxl-6">
                <div class="vs-blog blog-box grid-style">
                    <div class="blog-img">
                        <img src="<?= Url::base(true) ?>/themes/oasis/assets/img/banner-noticia2.jpg" alt="Blog">
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <a href="#"><i class="fal fa-user"></i>Автор</a>
                            <a href="#"><i class="fal fa-calendar-alt"></i>29.06.2025</a>
                        </div>
                        <h3 class="blog-title"><a href="#">Открытый бесплатный проект.</a></h3>
                        <p class="blog-text">"Это полностью открытый и бесплатный проект.   Если вам понравилось — вы можете просто сказать «спасибо»… или кинуть пару монет на кофе в USDT (TRC-20), через мобильное приложение по QR коду, или же через обменник в браузере по ссылке ниже."</p>
                        <a
        href="javascript:void(0);"
        class="menu-style2"
        onclick="
            navigator.clipboard.writeText('TQAT6fNJBhaqTnjwRYVeHFtsbh576iYqPk');
            window.open('https://exchange.mercuryo.io/?to=usdt&network=TRC20', '_blank');
        "
    >
        Скопировать адрес кошелька и открыть обменник
    </a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="row">
                    <div class="col-md-6 col-xxl-12">
                        <div class="vs-blog blog-box list-style">
                            <div class="blog-img">
                                <img src="<?= Url::base(true) ?>/themes/oasis/assets/img/banner-noticia.jpg" alt="Blog">
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <a href="#"><i class="fal fa-user"></i>Автор</a>
                                    <a href="#"><i class="fal fa-calendar-alt"></i>29.06.2025</a>
                                </div>
                                <h3 class="blog-title"><a href="#">От автора</a></h3>
                                <p class="blog-text">"Проделана огромная  работа что бы это заработало, все для вас дорогие фанаты Lineage 2. Искрене надеюсь что смог вас порадовать, я и правда старался для вас."</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl-12">
                        <div class="vs-blog blog-box list-style">
                            <div class="blog-img">
                                <img src="<?= Url::base(true) ?>/themes/oasis/assets/img/banner-anuncios.jpg" alt="Blog">
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <a href="#"><i class="fal fa-user"></i>Автор</a>
                                    <a href="#"><i class="fal fa-calendar-alt"></i>29.06.2025</a>
                                </div>
                                <h3 class="blog-title"><a href="#">Только в перед.</a></h3>
                                <p class="blog-text">"Никаких устаревших решений, все самые свежие фишки уже здесь с Yii 2 + Bootstrap 5 на PHP 8.2"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-wrapper footer-layout3">
    <div class="widget-area">
        <div class="parallax" data-bg-class="bg-dark" data-parallax-image="<?= Url::base(true) ?>/themes/oasis/assets/img/bg/map-bg-1-1.png"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="row justify-content-between">
                        <div class="col-md-6 col-lg-4 col-xl-auto">
                            <h2>Oasis</h2>
                            <p>Opensource</p>
                        </div>
                        <div class="col-md-12 col-lg-3 col-xl-auto">
                            <img src="<?= Url::base(true) ?>/themes/oasis/assets/img/logo.png" alt="Logo">
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-auto">
                            <h2>Контакты</h2>
                            <p class="footer-info"><i class="fab fa-facebook-f"></i>Facebook: <a href="https://www.facebook.com/groups/1799664900688601" target="_blank">OASIS</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap text-center">
        <div class="container">
            <p>&copy; Copyright 2025 <a class="text-theme2" href="https://oasis.gamer.gd">oasis.gamer.gd</a></p>
        </div>
    </div>
</footer>

<!-- Scroll To Top -->
<a href="#" class="scrollToTop scroll-btn"><i class="far fa-arrow-up"></i></a>

<!-- JS -->
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/vendor/jquery-3.6.0.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/bootstrap.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/slick.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/SmoothScroll.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/universal-parallax.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/jquery.magnific-popup.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/imagesloaded.pkgd.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/isotope.pkgd.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/vscustom-carousel.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/oasis/assets/js/main.js"></script>

<!-- Сохранение позиции скролла и защита от href="#" -->
<script>
(function() {
  try {
    var key = 'scroll:' + location.pathname + location.search;

    // Восстановить позицию при загрузке
    document.addEventListener('DOMContentLoaded', function () {
      var y = sessionStorage.getItem(key);
      if (y !== null) {
        var pos = parseInt(y, 10);
        if (!isNaN(pos)) window.scrollTo(0, pos);
      }
    });

    // Запомнить перед уходом/перезагрузкой
    window.addEventListener('beforeunload', function () {
      sessionStorage.setItem(key, String(window.pageYOffset || document.documentElement.scrollTop || 0));
    });

    // Не прыгать вверх на ссылках "#", кроме кнопки "вверх"
    document.addEventListener('click', function (e) {
      var a = e.target.closest && e.target.closest('a[href="#"]');
      if (a && !a.classList.contains('scrollToTop')) {
        e.preventDefault();
      }
    });
  } catch (e) { /* молча, чтобы ничего не ломать */ }
})();
</script>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
