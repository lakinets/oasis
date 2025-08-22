<?php

return [
    // --- Общие настройки ---
    'adminEmail'  => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName'  => 'L2 Legacy',

    // --- Модуль регистрации ---
    'register' => [
        'allow'             => true,    // включить/отключить регистрацию
        'confirm_email'     => true,    // подтверждение email
        'confirm_email.time'=> 60,      // минуты жизни ссылки
        'captcha' => [
            'allow' => true,            // показывать капчу
        ],
    ],

    // --- Поле префикса логина ---
    'prefixes' => [
        'allow'          => false,      // показывать поле «Префикс»
        'length'         => 3,          // длина префикса
        'count_for_list' => 5,          // сколько случайных префиксов выводить
    ],

    // --- Реферальная программа ---
    'referral_program' => [
        'allow' => true,                // включить рефералов
    ],
    'cookie_referer_name' => '_ref',    // имя куки реферала

    // --- Админка ---
    'backend_enabled' => true,          // доступ к /backend

    // --- Виджет статуса серверов ---
    'server_status.allow' => ['*'],     // '*' = показывать всем
];