<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV')   or define('YII_ENV', 'dev');

// ---------- Тема и конфиги серверов ----------
$themeName = null;
$dbConfig  = [];
$servers   = [];

if (is_file(__DIR__ . '/db.php')) {
    $dbConfig = require __DIR__ . '/db.php';
    try {
        $pdo = new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $themeName = $pdo->query('SELECT value FROM config WHERE param="theme" LIMIT 1')->fetchColumn() ?: null;
        $servers   = $pdo->query('SELECT * FROM servers_config')->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {
        error_log('DB load error: ' . $e->getMessage());
    }
}

$themeName = $themeName ?: 'ghtweb';

// ---------- Алиасы ----------
\Yii::setAlias('@themesRoot', dirname(__DIR__, 2) . '/themes');
\Yii::setAlias('@themes',    "@themesRoot/{$themeName}");
\Yii::setAlias('@modules',   dirname(__DIR__, 2) . '/modules');
\Yii::setAlias('@backend',   dirname(__DIR__) . '/modules/backend');

if (!is_dir(\Yii::getAlias('@themes'))) {
    $themeName = 'ghtweb';
    \Yii::setAlias('@themes', "@themesRoot/{$themeName}");
}

return [
    'id'         => 'l2-legacy',
    'name'       => 'L2 Legacy',
    'basePath'   => dirname(__DIR__),
    'language'   => 'ru',
    'timeZone'   => 'Europe/Moscow',
    'layout'     => "@themes/views/layouts/main",
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'bootstrap' => ['log'],

    'components' => [
        'request' => [
            'cookieValidationKey' => 'Qt0TUb2LME-ES9T5l5srJJh2n9QAbbwT',
            'enableCsrfValidation' => true,
        ],

        'view' => [
            'theme' => [
                'class'    => \yii\base\Theme::class,
                'basePath' => '@themes',
                'baseUrl'  => "@web/themes/{$themeName}",
                'pathMap'  => [
                    '@app/views'   => '@themes/views',
                    '@app/widgets' => '@themes/widgets',
                ],
            ],
        ],

        /* ---------- URL-правила ---------- */
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [

                /* BACKEND */
                'backend' => 'backend/default/index',
                'backend/<controller:\w+>' => 'backend/<controller>/index',
                'backend/<controller:\w+>/<action:\w+>' => 'backend/<controller>/<action>',

                /* АВТОРИЗАЦИЯ / РЕГИСТРАЦИЯ */
                'login'    => 'login/index',
                'logout'   => 'login/logout',
                'register' => 'register/index',

                /* ОСТАЛЬНЫЕ МОДУЛИ */
                'cabinet'  => 'cabinet/default/index',
                'cabinet/<controller:\w+>/<action:\w+>' => 'cabinet/<controller>/<action>',
                'cabinet/<controller:\w+>/<action:\w+>/<id:\d+>' => 'cabinet/<controller>/<action>/<id>',

                'stats'    => 'stats/default/index',
                'news'     => 'news/index',
                'gallery'  => 'gallery/index',

                /* СТАТИЧНЫЕ СТРАНИЦЫ */
                'page/<page:\w+>' => 'site/view',

                /* СТАНДАРТНОЕ */
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',

                /* Универсальное – В САМЫЙ КОНЕЦ */
                '<slug:[\w\-]+>' => 'page/view',
            ],
        ],

        'cache'       => ['class' => \yii\caching\FileCache::class],
        'user'        => [
            'identityClass'   => \app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl'        => ['/login/index'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => $dbConfig,

        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            'useFileTransport' => YII_DEBUG,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                ['class' => \yii\log\FileTarget::class, 'levels' => ['error', 'warning', 'info']],
            ],
        ],

        'i18n' => [
            'translations' => [
                'main' => [
                    'class'      => \yii\i18n\PhpMessageSource::class,
                    'basePath'   => '@app/messages',
                    'sourceLanguage' => 'ru',
                ],
                'backend*' => [
                    'class'      => \yii\i18n\PhpMessageSource::class,
                    'basePath'   => '@app/messages/backend',
                    'sourceLanguage' => 'en-US',
                    'fileMap'    => ['backend' => 'backend.php'],
                ],
            ],
        ],

        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl'  => '@web/assets',
            'appendTimestamp' => true,
            'forceCopy' => YII_DEBUG,
        ],

        'session' => [
            'class'   => \yii\web\Session::class,
            'timeout' => 3600,
        ],
    ],

    'modules' => [
        'backend' => [
            'class'  => \app\modules\backend\Module::class,
            'layout' => '@app/modules/backend/views/layouts/main',
        ],
        'cabinet' => ['class' => \app\modules\cabinet\Module::class],
        'register' => ['class' => \app\modules\register\RegisterModule::class],
        'gallery'  => ['class' => \app\modules\gallery\GalleryModule::class],
        'stats'    => ['class' => \app\modules\stats\StatsModule::class],
        'news'     => ['class' => \app\modules\news\NewsModule::class],
        'install'  => ['class' => \app\modules\install\InstallModule::class],
        'forgottenPassword' => ['class' => \app\modules\forgottenPassword\ForgottenPasswordModule::class],
        'deposit'  => ['class' => \app\modules\deposit\DepositModule::class],
        'page'     => ['class' => \app\modules\page\PageModule::class],
    ],

    'params' => array_merge(
        require __DIR__ . '/params.php',
        ['servers' => $servers]
    ),
];