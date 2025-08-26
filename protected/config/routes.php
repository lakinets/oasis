<?php
return [
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

    // Callback-эндпоинты для всех платёжных систем
    'POST cabinet/deposit/robokassa-callback'   => '/cabinet/deposit/robokassa-callback',
    'POST cabinet/deposit/unitpay-callback'     => '/cabinet/deposit/unitpay-callback',
    'POST cabinet/deposit/nowpayments-callback' => '/cabinet/deposit/nowpayments-callback',
    'POST cabinet/deposit/payop-callback'       => '/cabinet/deposit/payop-callback',
    'POST cabinet/deposit/cryptomus-callback'   => '/cabinet/deposit/cryptomus-callback',
    'POST cabinet/deposit/volet-callback'       => '/cabinet/deposit/volet-callback',
];