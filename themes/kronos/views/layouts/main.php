<?php
/**
 * Kronos → Yii2 (final, точные роуты по конфигу)
 * @var yii\web\View $this
 * @var string $content
 */
use yii\helpers\Url;
use yii\helpers\Html;

$assetsUrl = '/themes/kronos/assets';
$this->beginPage();
?><!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($this->title ?? 'Kronos Portal') ?></title>
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
            <!-- логотип -->
            <div id="header">
					<a href="/" class="logo-link">
					<img src="/themes/kronos/assets/images/logo.png" alt="Logo">
					</a>
				</div>

            <!-- кнопки -->
            <div id="buttons">
                <a href="<?= Url::to(['/page/patch']) ?>" id="reg_button">Скачать патч</a>
			<?php if (Yii::$app->user->isGuest): ?>
				<a href="<?= Url::to(['/page/start']) ?>" id="start_button">БЫСТРЫЙ<br>СТАРТ</a>
			<?php else: ?>
				<a href="<?= Url::to(['/cabinet/characters']) ?>" id="start_button">Личный<br>кабинет</a>
			<?php endif; ?>
							<a href="<?= Url::to(['page/client']) ?>" id="load_button">Скачать Клиент</a>
						</div>

            <!-- меню -->
            <div id="menu">
                <a href="<?= Url::to(['/']) ?>"><span>Г</span>ЛАВНАЯ</a>
                <a href="<?= Url::to(['/page/faq']) ?>"><span>F</span>AQ</a>
                <a href="<?= Url::to(['/page/download']) ?>"><span>З</span>АГРУЗКИ</a>
                <?php if (Yii::$app->user->isGuest): ?>
				<a href="<?= Url::to(['/register']) ?>"><span>Р</span>ЕГИСТРАЦИЯ</a>
			<?php else: ?>
				<a href="<?= Url::to(['/cabinet/deposit']) ?>"><span>П</span>ОПОЛНЕНИЕ</a>
			<?php endif; ?>
					<!-- ПОДДЕРЖКА (внешний форум) -->
			<a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/" target="_blank" rel="noopener"><span>П</span>ОДДЕРЖКА</a>
                <a href="<?= Url::to(['/stats']) ?>"><span>С</span>ТАТИСТИКА</a>
                <a href="<?= Url::to(['/page/about']) ?>"><span>О</span> СЕРВЕРЕ</a>
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
                                 <a href="<?= Url::to(['/login']) ?>" id="donate_button">Вход в кабинет</a>
                            </form>
                            <div id="login_links">
                                <a href="<?= Url::to(['/register']) ?>">Регистрация</a><br>
                                <a href="<?= Url::to(['/request-password-reset']) ?>">Забыли пароль?</a>
                            </div>
                        <?php else: ?>
                            Добро пожаловать, <?= htmlspecialchars(Yii::$app->user->identity->login) ?>!
                            <a href="<?= Url::to(['/logout']) ?>" data-method="post">Выйти</a>
                        <?php endif; ?>
												<!-- territory -->
						<div id="territory_block">
							<?= \app\widgets\RaceStat\RaceStatWidget::widget() ?>
						</div>
                        <!-- форум -->
                        <div id="forum_block">
                            <div class="theme">
                                <div class="theme_title"><a href="#">Ретактировать в main.php 129-146 строка.</a></div>
                                <div class="theme_info">написал <a href="#">Admin</a>, в 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#">Ну или подключить сюда флрум.</a></div>
                                <div class="theme_info">написал <a href="#">User</a>, v 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#">Оружие и доспехи</a></div>
                                <div class="theme_info">написал <a href="#">User</a>, v 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#">Торговля предметами</a></div>
                                <div class="theme_info">написал <a href="#">User</a>, v 15:56</div>
                            </div>
                            <div class="theme">
                                <div class="theme_title"><a href="#">Боевая экипировка</a></div>
                                <div class="theme_info">написал <a href="#">User</a>, v 15:56</div>
                            </div>
                        </div>
                    </div><!-- /left_block -->

                    <!-- правая колонка – новости + $content -->
                    <div id="right_block">
                        <div id="page_title"><span>ИНФОРМАЦИЯ</span></div>


                        <!-- основной контент страницы -->
                        <?= $content ?>
                    </div><!-- /right_block -->
                </div>
            </div>

            <!-- футер -->
            <div id="footer">
						<a href="https://github.com/lakinets/oasis" title="Скачать Oasis" id="dkarts" target="_blank" rel="noopener">
</a>
               &copy; Copyright 2025 <a class="copy" href="https://oasis.gamer.gd">oasis.gamer.gd</a>
            </div>

        </div><!-- /wrapper -->
    </div><!-- /body_bottom -->
</div><!-- /body_top -->

   <!-- Скрипт убрерает эффет подпрыгивания -->
<script>
(function() {
    // 1. ПРИНУДИТЕЛЬНО отключаем автоматический скролл браузера
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const CONFIG = {
        containerSelector: '#content', // ID блока, который меняем
        linkSelector: 'a[href]:not([href^="#"]):not([target])'
    };

    document.addEventListener('click', function(e) {
        const a = e.target.closest(CONFIG.linkSelector);
        
        // Проверка: ссылка должна быть внутренней и вести на ту же область
        if (!a || a.hostname !== location.hostname) return;

        e.preventDefault();
        loadPage(a.href, true);
    });

    function loadPage(url, pushState) {
        const container = document.querySelector(CONFIG.containerSelector);
        if (!container) {
            location.href = url;
            return;
        }

        // 2. ЗАМОРОЗКА СКРОЛЛА
        // Запоминаем текущую позицию
        const scrollY = window.pageYOffset || document.documentElement.scrollTop;
        // Фиксируем текущую высоту body, чтобы страница не "схлопнулась"
        document.body.style.minHeight = document.documentElement.scrollHeight + 'px';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                const newContent = newDoc.querySelector(CONFIG.containerSelector);
                const newTitle = newDoc.querySelector('title');

                if (newContent) {
                    // 3. ОБНОВЛЕНИЕ КОНТЕНТА
                    container.innerHTML = newContent.innerHTML;
                    if (newTitle) document.title = newTitle.innerText;

                    if (pushState) {
                        history.pushState({ scrollY: scrollY }, '', url);
                    }

                    // 4. ВОССТАНОВЛЕНИЕ
                    // Сначала восстанавливаем скролл
                    window.scrollTo(0, scrollY);

                    // Даем браузеру время отрисовать DOM и картинки, потом снимаем фиксацию высоты
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

    // Обработка кнопки "Назад" (Popstate)
    window.addEventListener('popstate', function(e) {
        // Если в истории есть сохраненный скролл — используем его, иначе перезагружаем
        if (e.state && typeof e.state.scrollY !== 'undefined') {
            loadPage(location.href, false);
        } else {
            location.reload();
        }
    });
})();
</script>