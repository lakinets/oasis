<?php

return [
    // --- Общие настройки ---
    'adminEmail'  => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName'  => 'L2 Legacy',

    // --- Модуль регистрации ---
    'register' => [
        'allow'             => true,
        'confirm_email'     => true,
        'confirm_email.time'=> 60,
        'captcha' => [
            'allow' => true,
        ],
    ],

    // --- Поле префикса логина ---
    'prefixes' => [
        'allow'          => false,
        'length'         => 3,
        'count_for_list' => 5,
    ],

    // --- Реферальная программа ---
    'referral_program' => [
        'allow' => true,
    ],
    'cookie_referer_name' => '_ref',

    // --- Админка ---
    'backend_enabled' => true,

    // --- Виджет статуса серверов ---
    'server_status.allow' => ['*'],

    // --- Восстановление пароля ---
    'mail.sender'           => 'noreply@site.ru',
    'mail.senderName'       => 'Сайт',
    'auth.resetTokenExpire' => 3600, // секунды
];