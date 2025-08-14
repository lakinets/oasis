<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use app\modules\backend\models\Tickets;

/** @var yii\web\View $this */
/** @var string $content */

$this->registerCsrfMetaTags();
$this->registerAssetBundle(\yii\bootstrap5\BootstrapAsset::class);

$assetUrl = Yii::getAlias('@web/themes/backend/assets');

// Подключаем скрипты и стили
$this->registerCssFile('https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
$this->registerCssFile($assetUrl . '/css/style.css');
$this->registerCssFile($assetUrl . '/css/notifications.css');

$this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.8/handlebars.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($assetUrl . '/js/notification.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($assetUrl . '/js/serializeForm.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile($assetUrl . '/js/main.js', ['position' => \yii\web\View::POS_END]);

// Меню
$menuItems = [
    ['label' => Yii::t('backend', 'Главная'), 'url' => ['/backend/default/index']],
    ['label' => Yii::t('backend', 'Страницы'), 'url' => ['/backend/pages/index']],
    ['label' => Yii::t('backend', 'Новости'), 'url' => ['/backend/news/index']],
    ['label' => Yii::t('backend', 'Юзеры'), 'url' => ['/backend/users/index']],
    ['label' => Yii::t('backend', 'Настройки'), 'url' => ['/backend/config/index']],
    [
        'label' => 'Lineage',
        'items' => [
            ['label' => Yii::t('main', 'Игровые сервера'), 'url' => ['/backend/game-servers/index']],
            ['label' => Yii::t('main', 'Логин сервера'), 'url' => ['/backend/login-servers/index']],
        ],
    ],
    ['label' => Yii::t('backend', 'Пополнения баланса'), 'url' => ['/backend/transactions/index']],
    ['label' => Yii::t('backend', 'Галерея'), 'url' => ['/backend/gallery/index']],
    [
        'label' => Yii::t('main', 'Бонусы'),
        'items' => [
            ['label' => Yii::t('main', 'Просмотр'), 'url' => ['/backend/bonuses/index']],
            ['label' => Yii::t('main', 'Коды'), 'url' => ['/backend/bonuses/codes']],
        ],
    ],
    [
        'label' => Yii::t('main', 'Тикеты') . ' <span class="badge bg-primary">' . Tickets::find()->where(['updated_at' => null])->count() . '</span>',
        'encode' => false,
        'items' => [
            ['label' => Yii::t('main', 'Просмотр'), 'url' => ['/backend/tickets/index']],
            ['label' => Yii::t('main', 'Категории'), 'url' => ['/backend/tickets/categories']],
        ],
    ],
];
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode('GHTWEB / Backend') ?></title>
    <?php $this->head() ?>
    <script>
        const CSRF_TOKEN_NAME = '<?= Yii::$app->request->csrfParam ?>';
        const CSRF_TOKEN_VALUE = '<?= Yii::$app->request->csrfToken ?>';
        const APP = {};
    </script>
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Url::to(['/backend/default/index']) ?>">GHTWEB Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <?= Nav::widget([
                'options' => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0'],
                'items' => $menuItems,
                'encodeLabels' => false,
            ]) ?>
        </div>
    </div>
</nav>

<div class="wrapper mt-5 pt-4">
    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => ['label' => Yii::t('main', 'Главная'), 'url' => ['/backend/default/index']],
            'links' => $this->params['breadcrumbs'] ?? [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="site-footer bg-light py-3 mt-5">
    <div class="container text-center">
        &copy; <a href="#">GHTWEB X</a> <?= date('Y') ?>
    </div>
</footer>

<script type="text/x-handlebars-template" id="modal-box-tpl">
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{{title}}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">{{{body}}}</div>
            </div>
        </div>
    </div>
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
