<?php
use yii\bootstrap5\Nav;
use yii\bootstrap5\BootstrapAsset;
use yii\helpers\Html;
use yii\helpers\Url;

BootstrapAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Навбар со всеми пунктами -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Url::to(['/backend']) ?>">Oasis</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0'],
                'encodeLabels' => false,
                'items' => [
                    ['label' => 'Главная', 'url' => ['/backend/default/index'], 'linkOptions' => ['class' => 'nav-link']],
                    ['label' => 'Страницы', 'url' => ['/backend/pages/index'], 'linkOptions' => ['class' => 'nav-link']],
                    ['label' => 'Новости', 'url' => ['/backend/news/index'], 'linkOptions' => ['class' => 'nav-link']],
                    ['label' => 'Юзеры', 'url' => ['/backend/users/index'], 'linkOptions' => ['class' => 'nav-link']],
                    ['label' => 'Настройки', 'url' => ['/backend/config/index'], 'linkOptions' => ['class' => 'nav-link']],
                    [
                        'label' => 'Lineage',
                        'items' => [
                            ['label' => 'Игровые сервера', 'url' => ['/backend/game-servers/index']],
                            ['label' => 'Логин сервера', 'url' => ['/backend/login-servers/index']],
                        ],
                    ],
                    [
                        'label' => 'Бонусы',
                        'items' => [
                            ['label' => 'Просмотр', 'url' => ['/backend/bonuses/index']],
                            ['label' => 'Коды', 'url' => ['/backend/bonuses/codes']],
                        ],
                    ],
                    [
                        'label' => 'Тикеты',
                        'items' => [
                            ['label' => 'Просмотр', 'url' => ['/backend/tickets/index']],
                            ['label' => 'Категории', 'url' => ['/backend/tickets/categories']],
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>