<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Url::to(['/backend']) ?>">Oasis</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/default/index']) ?>">Главная</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/pages/index']) ?>">Страницы</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/news/index']) ?>">Новости</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/users/index']) ?>">Пользователи</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/config/index']) ?>">Настройки</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/game-servers/index']) ?>">Серверы</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/bonuses/index']) ?>">Бонусы</a></li>
            </ul>
        </div>
    </div>
</nav>