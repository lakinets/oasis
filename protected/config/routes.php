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
	    // ... ваши прежние правила ...
    'POST cabinet/deposit/robokassa-callback'   => '/cabinet/deposit/robokassa-callback',
    'POST cabinet/deposit/unitpay-callback'     => '/cabinet/deposit/unitpay-callback',

    // новые IPN/Result маршруты:
    'POST cabinet/deposit/nowpayments-callback' => '/cabinet/deposit/nowpayments-callback',
    'POST cabinet/deposit/payop-callback'       => '/cabinet/deposit/payop-callback',
    'POST cabinet/deposit/cryptomus-callback'   => '/cabinet/deposit/cryptomus-callback',
];
