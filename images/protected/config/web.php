<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

// Значение темы по умолчанию
$themeName = 'ghtweb';
$dbConfig = [];

if (file_exists(__DIR__ . '/db.php')) {
    $dbConfig = require __DIR__ . '/db.php';
    try {
        $pdo = new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT value FROM config WHERE param = :param LIMIT 1');
        $stmt->execute([':param' => 'theme']);
        $themeNameFromDb = $stmt->fetchColumn();
        if (!empty($themeNameFromDb)) {
            $themeName = $themeNameFromDb;
        }
    } catch (\Throwable $e) {
        if (YII_DEBUG) {
            error_log('Theme load error: ' . $e->getMessage());
        }
    }
}

// Алиасы
\Yii::setAlias('@themesRoot', dirname(__DIR__, 2) . '/themes');
\Yii::setAlias('@themes', "@themesRoot/{$themeName}");
\Yii::setAlias('@modules', dirname(__DIR__, 2) . '/modules');
\Yii::setAlias('@app', dirname(__DIR__));

if (!is_dir(\Yii::getAlias('@themes'))) {
    $themeName = 'ghtweb';
    \Yii::setAlias('@themes', "@themesRoot/{$themeName}");
}

return [
    'id' => 'l2-legacy',
    'name' => 'L2 Legacy',
    'basePath' => dirname(__DIR__),
    'language' => 'ru',
    'timeZone' => 'Europe/Moscow',
    'layout' => "@themes/views/layouts/main",
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],

    'bootstrap' => ['log'],

    'components' => [

        'request' => [
            'cookieValidationKey' => 'Qt0TUb2LME-ES9T5l5srJJh2n9QAbbwT',
            'enableCsrfValidation' => true,
        ],

        'view' => [
            'theme' => [
                'class' => \yii\base\Theme::class,
                'basePath' => '@themes',
                'baseUrl' => "@web/themes/{$themeName}",
                'pathMap' => [
                    '@app/views' => '@themes/views',
                    '@app/widgets' => '@themes/widgets',
                ],
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'backend' => 'backend/default/index',
                'backend/<controller:\w+>' => 'backend/<controller>/index',
                'backend/<controller:\w+>/<action:\w+>' => 'backend/<controller>/<action>',

                'backend/users' => 'backend/users/index',
                'backend/users/<action:\w+>' => 'backend/users/<action>',
                'backend/users/<action:\w+>/<id:\d+>' => 'backend/users/<action>/<id>',

                'install' => 'install/index',
                'register' => 'register/index',
                'stats' => 'stats/default/index',
                'news' => 'news/index',
                'gallery' => 'gallery/index',
                'login' => 'login/index',
                'cabinet' => 'cabinet/index',
                'page/<page:\w+>' => 'site/view',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],

        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],

        'user' => [
            'identityClass' => \app\modules\backend\models\Users::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/backend/default/login'],
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
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'i18n' => [
            'translations' => [
                'main' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'ru',
                ],
                'backend*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/messages/backend',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'backend' => 'backend.php',
                    ],
                ],
            ],
        ],

        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'appendTimestamp' => true,
            'forceCopy' => YII_DEBUG,
        ],

        'session' => [
            'class' => \yii\web\Session::class,
            'timeout' => 3600,
        ],
    ],

    'modules' => [
        'backend' => [
            'class' => \app\modules\backend\Module::class,
            'layout' => '@app/modules/backend/views/layouts/main',
        ],
        'cabinet' => [
            'class' => \app\modules\cabinet\CabinetModule::class,
        ],
        'register' => [
            'class' => \app\modules\register\RegisterModule::class,
        ],
        'gallery' => [
            'class' => \app\modules\gallery\GalleryModule::class,
        ],
        'stats' => [
            'class' => \app\modules\stats\StatsModule::class,
        ],
        'news' => [
            'class' => \app\modules\news\NewsModule::class,
        ],
        'install' => [
            'class' => \app\modules\install\InstallModule::class,
        ],
        'login' => [
            'class' => \app\modules\login\LoginModule::class,
        ],
        'forgottenPassword' => [
            'class' => \app\modules\forgottenPassword\ForgottenPasswordModule::class,
        ],
        'deposit' => [
            'class' => \app\modules\deposit\DepositModule::class,
        ],
        'page' => [
            'class' => \app\modules\page\PageModule::class,
        ],
    ],

    'params' => require __DIR__ . '/params.php',
];
