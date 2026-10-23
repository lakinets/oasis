<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV')   or define('YII_ENV', 'dev');

// ---------- Тема и конфиги серверов ----------
$themeName = 'oasis';   // дефолт
$adminUrl  = 'backend'; // дефолт для админки
$dbConfig  = [];
$servers   = [];

if (is_file(__DIR__ . '/db.php')) {
    $dbConfig = require __DIR__ . '/db.php';
    try {
        $pdo = new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $themeName = $pdo->query('SELECT value FROM config WHERE param="theme" LIMIT 1')->fetchColumn() ?: 'oasis';
        
        // Читаем кастомный URL админки
        $customAdminUrl = $pdo->query('SELECT value FROM config WHERE param="admin.url" LIMIT 1')->fetchColumn();
        if ($customAdminUrl && preg_match('/^[a-zA-Z0-9_-]+$/', $customAdminUrl)) {
            $adminUrl = $customAdminUrl;
        }
        
        $servers = $pdo->query('SELECT * FROM servers_config')->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {
        $themeName = 'oasis';
        $adminUrl  = 'backend';
        $servers   = [];
    }
}

// ---------- Алиасы ----------
\Yii::setAlias('@themesRoot', dirname(__DIR__, 2) . '/themes');
\Yii::setAlias('@themes',    "@themesRoot/{$themeName}");
\Yii::setAlias('@modules',   dirname(__DIR__, 2) . '/modules');
\Yii::setAlias('@backend',   dirname(__DIR__) . '/modules/backend');

if (!is_dir(\Yii::getAlias('@themes'))) {
    $themeName = 'oasis';
    \Yii::setAlias('@themes', "@themesRoot/{$themeName}");
}

return [
    'id'             => 'l2-legacy',
    'name'           => 'L2 Legacy',
    'basePath'       => dirname(__DIR__),
    'language'       => 'ru',
    'sourceLanguage' => 'ru',
    'timeZone'       => 'Europe/Moscow',
    'layout'         => "@themes/views/layouts/main",
    'vendorPath'     => dirname(__DIR__, 2) . '/vendor',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'bootstrap' => ['language-selector', 'profile-initializer'],

    'components' => [
        'request' => [
            'cookieValidationKey' => 'Qt0TUb2LME-ES9T5l5srJJh2n9QAbbwT',
            'enableCsrfValidation' => true,
        ],

        'language-selector' => [
            'class' => \app\components\LanguageSelector::class,
        ],

        'profile-initializer' => [
            'class' => \app\components\ProfileInitializer::class,
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

        'captcha' => [
            'class' => \yii\captcha\CaptchaAction::class,
            'minLength' => 4,
            'maxLength' => 5,
            'width'  => 100,
            'height' => 40,
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules' => [
                'install' => 'install/setup/index',
                'install/<action:\w+>' => 'install/setup/<action>',
                'install/<action:\w+>/' => 'install/setup/<action>',

                'language/<lang:[a-z]{2}>' => 'site/language',

                // --- ДИНАМИЧЕСКИЙ РОУТИНГ АДМИНКИ ---
                $adminUrl => 'backend/default/index',
                $adminUrl . '/<controller:\w+>' => 'backend/<controller>/index',
                $adminUrl . '/<controller:\w+>/<action:\w+>' => 'backend/<controller>/<action>',
                // -------------------------------------

                'login'    => 'auth/default/login',
                'logout'   => 'auth/default/logout',
                'register' => 'register/index',

                'request-password-reset' => 'auth/default/request-password-reset',
                'reset-password'         => 'auth/default/reset-password',

                'cabinet'  => 'cabinet/default/index',
                'cabinet/<controller:\w+>/<action:\w+>' => 'cabinet/<controller>/<action>',
                'cabinet/<controller:\w+>/<action:\w+>/<id:\d+>' => 'cabinet/<controller>/<action>/<id>',

                'stats'    => 'stats/default/index',
                'news'     => 'news/index',
                'gallery'  => 'gallery/index',

                'page/<page:\w+>' => 'site/view',

                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<slug:[\w\-]+>' => 'site/page',
            ],
        ],

        'cache'       => ['class' => \yii\caching\FileCache::class],
        'user' => [
            'identityClass'   => 'app\models\Users',
            'enableAutoLogin' => true,
            'loginUrl'        => ['/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => $dbConfig,

        'mailer' => [
            'class'            => \yii\swiftmailer\Mailer::class,
            'useFileTransport' => YII_DEBUG,
            'transport'        => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'ssl://smtp.yandex.ru',
                'username'   => 'noreply@site.ru',
                'password'   => '********',
                'port'       => 465,
                'encryption' => 'ssl',
            ],
        ],

        'log' => [
            'traceLevel' => 0,
            'targets' => [],
        ],

        'i18n' => [
            'translations' => [

                'main*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['main' => 'main.php'],
                ],

                'views*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['views' => 'views.php'],
                ],

                'controllers*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['controllers' => 'controllers.php'],
                ],

                'models*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['models' => 'models.php'],
                ],

                'backend*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['backend' => 'backend.php'],
                ],

                'mail*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['mail' => 'mail.php'],
                ],

                'page*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['page' => 'page.php'],
                ],

                'auth*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['auth' => 'auth.php'],
                ],

                'install*' => [
                    'class'           => \yii\i18n\PhpMessageSource::class,
                    'basePath'        => '@app/messages',
                    'sourceLanguage'  => 'ru',
                    'forceTranslation'=> true,
                    'fileMap'         => ['install' => 'install.php'],
                ],

                'yii' => [
                    'class'          => \yii\i18n\PhpMessageSource::class,
                    'basePath'       => '@vendor/yiisoft/yii2/messages',
                    'sourceLanguage' => 'en-US',
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
        'backend'  => ['class' => \app\modules\backend\Module::class],
        'cabinet'  => ['class' => \app\modules\cabinet\Module::class],
        'register' => ['class' => \app\modules\register\RegisterModule::class],
        // 'gallery'  => ['class' => \app\modules\gallery\GalleryModule::class], // УДАЛЕНО
        'stats'    => ['class' => \app\modules\stats\StatsModule::class],
        // 'news'     => ['class' => \app\modules\news\NewsModule::class],       // УДАЛЕНО
        'forgottenPassword' => ['class' => \app\modules\forgottenPassword\ForgottenPasswordModule::class],
        'deposit'  => ['class' => \app\modules\deposit\DepositModule::class],
        'auth'     => ['class' => \app\modules\auth\Module::class],

        'install'  => ['class' => 'app\modules\install\InstallModule'],
    ],

    'params' => array_merge(
        require __DIR__ . '/params.php',
        ['servers' => $servers]
    ),
];