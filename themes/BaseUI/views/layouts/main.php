<?php
/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Url;
use yii\helpers\Html;

$baseUrl = '/themes/BaseUI/assets';
$this->beginPage();
?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody(); ?>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="logo-box">
            <img src="<?= $baseUrl ?>/images/logo.png" alt="L2Web" class="logo">
        </div>

        <!-- Переключатель языка (под логотипом) -->
        <div class="lang-switch">
            <?= \app\widgets\LanguageSwitcher\LanguageSwitcher::widget([
                'containerClass' => 'language-switcher',
                'activeClass'    => 'active',
                'showCurrent'    => true,
                'useFlags'       => true,
                'flagWidth'      => 26,
                'flagHeight'     => 18,
                'flagLinkStyle'  => 'display:inline-block; line-height:1;',
                'flagImgStyle'   => 'display:block; border-radius:2px;',
            ]) ?>
        </div>

        <!-- Виджет онлайна -->
        <div class="server-status-widget">
            <?= \app\widgets\ServerStatus\ServerStatusWidget::widget(['compact' => true]) ?>
        </div>

        <nav class="main-nav">
            <a href="<?= Url::home() ?>" class="nav-item"><?= Yii::t('main', 'ГЛАВНАЯ') ?></a>
            <a href="<?= Url::to(['/page/about']) ?>" class="nav-item"><?= Yii::t('main', 'О СЕРВЕРЕ') ?></a>
            <a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/"
               class="nav-item"
               target="_blank"
               rel="noopener"><?= Yii::t('main', 'ПОДДЕРЖКА') ?></a>

            <hr class="nav-divider">

            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Url::to(['/register']) ?>" class="nav-item highlight"><?= Yii::t('main', 'РЕГИСТРАЦИЯ') ?></a>
                <a href="<?= Url::to(['/login']) ?>" class="nav-item"><?= Yii::t('main', 'ВХОД') ?></a>
            <?php else: ?>
                <a href="<?= Url::to(['/cabinet/characters']) ?>" class="nav-item"><?= Yii::t('main', 'ПРОФИЛЬ') ?></a>
                <a href="<?= Url::to(['/cabinet/deposit']) ?>" class="nav-item"><?= Yii::t('main', 'БАЛАНС') ?></a>
                <a href="<?= Url::to(['/stats']) ?>" class="nav-item"><?= Yii::t('main', 'Статус') ?></a>
                <a href="<?= Url::to(['/logout']) ?>" data-method="post" class="nav-item logout"><?= Yii::t('main', 'ВЫЙТИ ИЗ ЛК') ?></a>
            <?php endif; ?>
        </nav>

        <!-- Виджет статистики рас -->
        <div class="race-status-widget">
            <?= \app\widgets\RaceStat\RaceStatWidget::widget() ?>
        </div>
    </aside>

    <div class="content" id="content">
        <?= $content ?>
    </div>
</div>

<!-- Скрипт убирает эффект подпрыгивания при внутренних переходах -->
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

        // Ссылки переключателя языка не перехватываем —
        // пусть браузер сам обработает Set-Cookie и редирект.
        if (a.closest('.lang-switch')) return;

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