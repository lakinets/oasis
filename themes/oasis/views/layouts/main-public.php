<?php
/**
 * @var yii\web\View $this
 * @var string $content
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->beginPage();
?>
<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= Html::encode($this->title ?? 'Lineage 2 Website') ?></title>
    <meta name="author" content="Angfuzsoft">
    <meta name="description" content="Spiel - Esports and Gaming HTML Template">
    <meta name="keywords" content="l2 oasis, c4, l2, lineage, lineage2, lineage 2">
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= Url::base(true) ?>/themes/orion/assets/img/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= Url::base(true) ?>/themes/orion/assets/img/favicons/apple-icon-60x60.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Url::base(true) ?>/themes/orion/assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= Url::base(true) ?>/themes/orion/assets/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?= Url::base(true) ?>/themes/orion/assets/img/favicons/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/orion/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/orion/assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/orion/assets/css/slick.min.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/orion/assets/css/style.css">
    <link rel="stylesheet" href="<?= Url::base(true) ?>/themes/orion/assets/css/module-override.css?v=<?= time() ?>">

    <?php $this->head() ?>
</head>
<body class="layout-public" data-route="<?= Yii::$app->request->getPathInfo() ?>">
<?php $this->beginBody(); ?>

<!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a>.</p>
<![endif]-->

<!-- Preloader -->
<div class="preloader">
    <button class="vs-btn preloaderCls">Cancel Preloader</button>
    <div class="preloader-inner">
        <div class="vs-loadholder">
            <div class="vs-loader">
                <span class="loader-text">
                    <img src="<?= Url::base(true) ?>/themes/orion/assets/img/loader.png" style="height: 90px; margin-top:20px;" alt="Loader">
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
            <a href="<?= Url::home() ?>"><img src="<?= Url::base(true) ?>/themes/orion/assets/img/logo.png" alt="Logo"></a>
        </div>
        <div class="vs-mobile-menu">
            <ul>
                <li><a href="<?= Url::home() ?>">Home</a></li>
                <li><a href="<?= Url::to(['/site/view', 'page' => 'infoserver']) ?>">Server Info</a></li>
                <li><a href="<?= Url::to(['/site/view', 'page' => 'descargas']) ?>">Downloads</a></li>
                <li><a href="https://www.facebook.com/">Contact Us</a></li>
            </ul>
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
                    <a href="<?= Url::home() ?>"><img src="<?= Url::base(true) ?>/themes/orion/assets/img/logo.png" alt="Logo"></a>
                </div>
                <p class="footer-about-text">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore...</p>
                <div class="multi-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest-p"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Header -->
<header class="vs-header header-layout3">
    <div class="container">
        <div class="sticky-wrapper">
            <div class="sticky-active">
                <div class="header-menu-area">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="header-logo">
                                <img src="<?= Url::base(true) ?>/themes/orion/assets/img/logo.png" alt="Logo">
                            </div>
                        </div>
                        <div class="col-auto">
                            <nav class="main-menu menu-style2 d-none d-lg-block">
                                <ul>
                                    <li><a href="<?= Url::home() ?>">Главная</a></li>
                                    <li><a href="<?= Url::to(['/page/files']) ?>">Файлы</a></li>
                                    <li><a href="<?= Url::to(['/register']) ?>">Регистрация</a></li>
                                    <li class="menu-item-has-children">
                                        <a href="#">Инфо</a>
                                        <ul class="sub-menu">
                                            <?= \app\widgets\ServerStatus\ServerStatusWidget::widget() ?>
                                        </ul>
                                    </li>
                                    <li><a href="/stats">Статус</a></li>
                                </ul>
                            </nav>
                            <button type="button" class="vs-menu-toggle d-inline-block d-lg-none">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <a href="<?= Url::to(['/login']) ?>" class="vs-btn outline-style d-none d-xl-block">
                                <i class="fab fa-expeditedssl"></i> Личный кабинет
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Hero Area -->
<section class="vs-hero-wrapper position-relative bg-dark">
    <div class="hero-social d-none d-lg-block">
        <a href="#" target="_blank"><span>Facebook</span></a>
    </div>
    <div class="vs-carousel" id="heroSlide1" data-slide-show="1" data-md-slide-show="1" data-fade="true">
        <div class="slider">
            <div class="hero-clip-slider">
                <div class="hero-clip-img" data-bg-src="<?= Url::base(true) ?>/themes/orion/assets/img/hero/hero-1-11.jpg"></div>
                <div class="hero-clip-shape bg-theme2"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-xxl-6 offset-xl-1">
                            <div class="hero-clip-content">
                                <h1 class="hero-clip-title">
                                    Lineage 2 <span class="text-theme2"><br>NEW WORLD</span><br>server x1 interlude
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<main>
    <?= $content ?>
</main>

<!-- Footer -->
<footer class="footer-wrapper footer-layout3">
    <div class="widget-area">
        <div class="parallax" data-bg-class="bg-dark" data-parallax-image="<?= Url::base(true) ?>/themes/orion/assets/img/bg/map-bg-1-1.png"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="row justify-content-between">
                        <div class="col-md-6 col-lg-4 col-xl-auto">
                            <h2>L2 Oasis</h2>
                            <p>Lorem Ipsum</p>
                        </div>
                        <div class="col-md-12 col-lg-3 col-xl-auto">
                            <img src="<?= Url::base(true) ?>/themes/orion/assets/img/logo.png" alt="Logo">
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-auto">
                            <h2>Contacto</h2>
                            <p class="footer-info"><i class="fab fa-facebook-f"></i> Facebook: <a href="https://www.facebook.com/#" target="_blank">L2OASIS</a></p>
                            <p class="footer-info"><i class="fab fa-discord"></i> Discord: <a href="#">#</a></p>
                            <p class="footer-info"><i class="fab fa-instagram"></i> Instagram: <a href="#" target="_blank">L2Website</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap text-center">
        <div class="container">
            <p>&copy; Copyright 2024 L2Oasis <a class="text-theme2" href="https://l2devs.com">By L2Devs</a></p>
        </div>
    </div>
</footer>

<!-- Scroll To Top -->
<a href="#" class="scrollToTop scroll-btn"><i class="far fa-arrow-up"></i></a>

<!-- JS -->
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/vendor/jquery-3.6.0.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/bootstrap.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/slick.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/SmoothScroll.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/universal-parallax.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/jquery.magnific-popup.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/imagesloaded.pkgd.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/isotope.pkgd.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/vscustom-carousel.min.js"></script>
<script src="<?= Url::base(true) ?>/themes/orion/assets/js/main.js"></script>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>