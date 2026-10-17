<?php

return [
    // --- Общие настройки ---
    'adminEmail'  => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName'  => 'L2 Legacy',

    // --- Мультиязычность ---
    'languages' => [
        'ru' => 'Русский',
        'en' => 'English',
    ],
    'defaultLanguage' => 'ru',

    // --- SEO-дефолты ---
    // Используются в layout, если в БД (таблица config, param meta.title/meta.description/meta.keywords)
    // значение пустое. Если админ заполнил в БД — берётся значение из БД.
    'meta' => [
        'title' => [
            'ru' => 'GHTWEB CMS для Lineage2 серверов',
            'en' => 'GHTWEB CMS for Lineage2 servers',
        ],
        'description' => [
            'ru' => 'GHTWEB CMS для Lineage2 серверов',
            'en' => 'GHTWEB CMS for Lineage2 servers',
        ],
        'keywords' => [
            'ru' => 'l2, lineage 2, lineage2, c4, interlude',
            'en' => 'l2, lineage 2, lineage2, c4, interlude',
        ],
    ],

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