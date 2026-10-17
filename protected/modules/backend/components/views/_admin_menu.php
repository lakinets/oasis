<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Url::to(['/backend']) ?>">Oasis</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/default/index']) ?>"><?= Yii::t('backend', 'Главная') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/pages/index']) ?>"><?= Yii::t('backend', 'Страницы') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/news/index']) ?>"><?= Yii::t('backend', 'Новости') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/users/index']) ?>"><?= Yii::t('backend', 'Пользователи') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/config/index']) ?>"><?= Yii::t('backend', 'Настройки') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/game-servers/index']) ?>"><?= Yii::t('backend', 'Серверы') ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/bonuses/index']) ?>"><?= Yii::t('backend', 'Бонусы') ?></a></li>
				<li class="nav-item"><a class="nav-link" href="<?= Url::to(['/backend/services-manager/index']) ?>"><?= Yii::t('backend', 'Управление сервисами') ?></a></li>
            </ul>
        </div>
    </div>
</nav>
