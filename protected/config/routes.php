<?php
return [
    /* ---------- BACKEND ---------- */
    'backend' => 'backend/default/index',
    'backend/bonuses' => 'backend/bonuses/index',
    'backend/config' => 'backend/config/index',
    'backend/gallery' => 'backend/gallery/index',
    'backend/news' => 'backend/news/index',
    'backend/pages' => 'backend/pages/index',
    'backend/tickets' => 'backend/tickets/index',
    'backend/users' => 'backend/users/index',
    'backend/login' => 'backend/login/index',
    'backend/transactions' => 'backend/transactions/index',
    'backend/game-servers' => 'backend/game-servers/index',
    'backend/login-servers' => 'backend/login-servers/index',

    /* ---------- SERVICES MANAGER (GET) ---------- */
    'backend/services-manager' => 'backend/services-manager/index',
    'backend/services-manager/update' => 'backend/services-manager/update',
    'backend/services-manager/toggle' => 'backend/services-manager/toggle',

    /* ---------- CALLBACK ЭНДПОИНТЫ ---------- */
    'POST cabinet/deposit/robokassa-callback'   => '/cabinet/deposit/robokassa-callback',
    'POST cabinet/deposit/unitpay-callback'     => '/cabinet/deposit/unitpay-callback',
    'POST cabinet/deposit/nowpayments-callback' => '/cabinet/deposit/nowpayments-callback',
    'POST cabinet/deposit/payop-callback'       => '/cabinet/deposit/payop-callback',
    'POST cabinet/deposit/cryptomus-callback'   => '/cabinet/deposit/cryptomus-callback',
    'POST cabinet/deposit/volet-callback'       => '/cabinet/deposit/volet-callback',

    /* ---------- CABINET (все контроллеры и экшены с дефисами) ---------- */
    'cabinet' => 'cabinet/default/index',
    'cabinet/<controller:[\w\-]+>/<action:[\w\-]+>' => 'cabinet/<controller>/<action>',
    'cabinet/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => 'cabinet/<controller>/<action>/<id>',

    /* ---------- ОСТАЛЬНЫЕ МОДУЛИ ---------- */
    'stats' => 'stats/default/index',
    'news' => 'news/index',
    'gallery' => 'gallery/index',

    'page/<page:[\w\-]+>' => 'site/view',
    '<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>',
    '<slug:[\w\-]+>' => 'site/page',
];