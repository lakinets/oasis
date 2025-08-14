<?php
declare(strict_types=1);

return [
    'basePath' => __DIR__,
    'name' => 'GHTWEB 5',
    'preload' => ['log'],
    'import' => [
        'application.models.*',
        'application.components.*',
    ],
    'defaultController' => 'site',
    'catchAllRequest' => ['site/index'],

    'controllerMap' => [
        'site' => 'SiteController',
    ],

    'components' => [
        'db' => [
            'connectionString' => 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            'emulatePrepare' => true,
            'username' => DB_USER,
            'password' => DB_PASS,
            'charset' => 'utf8mb4',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'class' => 'CLogRouter',
            'routes' => [
                [
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ],
            ],
        ],
    ],
];