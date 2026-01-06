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

        <!-- Виджет онлайна -->
        <div class="server-status-widget">
            <?= \app\widgets\ServerStatus\ServerStatusWidget::widget(['compact' => true]) ?>
        </div>

        <nav class="main-nav">
            <a href="<?= Url::to(['/']) ?>" class="nav-item">ГЛАВНАЯ</a>
            <a href="<?= Url::to(['/page/about']) ?>" class="nav-item">О СЕРВЕРЕ</a>
            <a href="https://forummaxi.ru/topic/100146-oasis-%D1%80%D0%B5%D0%BB%D0%B8%D0%B7/" 
			   class="nav-item" 
			   target="_blank" 
			   rel="noopener">ПОДДЕРЖКА</a>
            
            <hr class="nav-divider">
            
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Url::to(['/register']) ?>" class="nav-item highlight">РЕГИСТРАЦИЯ</a>
                <a href="<?= Url::to(['/login']) ?>" class="nav-item">ВХОД</a>
            <?php else: ?>
                <a href="<?= Url::to(['/cabinet/characters']) ?>" class="nav-item">ПРОФИЛЬ</a>
                <a href="<?= Url::to(['/cabinet/deposit']) ?>" class="nav-item">БАЛАНС</a>
                <a href="<?= Url::to(['/stats']) ?>" class="nav-item">Статус</a>
                <a href="<?= Url::to(['/logout']) ?>" data-method="post" class="nav-item logout">ВЫЙТИ ИЗ ЛК</a>
            <?php endif; ?>
        </nav>
					<!-- Виджет статистики рас -->
	        <div class="race-status-widget">
            <?= \app\widgets\RaceStat\RaceStatWidget::widget() ?>
        </div>
    </aside>
 

	
    <div class="content">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>

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