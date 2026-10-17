<?php
/**
 * English translations — install category (setup wizard).
 * sourceLanguage = 'ru', ключ = значение оригинала.
 */
return [

    // =========================================================
    // === Step 1: License and environment check ===
    // =========================================================
    'Шаг 1: Проверка хоста и лицензия' => 'Step 1: Host check and license',
    'Лицензионное соглашение Oasis CMS' => 'Oasis CMS License Agreement',
    'Я принимаю условия соглашения' => 'I accept the terms of the agreement',
    'Проверка окружения' => 'Environment check',
    'Да' => 'Yes',
    'Нет' => 'No',
    'Запись в' => 'Write access to',
    'Права на папку' => 'Permissions for folder',
    'Дальше' => 'Next',
    'Некоторые требования не выполнены ({count}). Установка может работать нестабильно.' => 'Some requirements are not met ({count}). The installation may work unstably.',
    'Продолжить несмотря на предупреждения?' => 'Continue despite warnings?',
    'Вы должны принять условия лицензии, чтобы продолжить установку.' => 'You must accept the license terms to continue installation.',

    // =========================================================
    // === Step 2: Database ===
    // =========================================================
    'Шаг 2: Подключение к базе данных.' => 'Step 2: Database connection.',
    'Дальше →' => 'Next →',

    // =========================================================
    // === Step 3: Migrations ===
    // =========================================================
    'Шаг 3: Установка таблиц' => 'Step 3: Tables installation',

    // =========================================================
    // === Step 4: Administrator ===
    // =========================================================
    'Шаг 4: Создание администратора' => 'Step 4: Administrator creation',
    'Завершить установку' => 'Finish installation',

    // =========================================================
    // === Step 5: Completion ===
    // =========================================================
    'Шаг 5: Завершение' => 'Step 5: Completion',
    'Установка завершена. Папка {folder} удалена автоматически.' => 'Installation completed. Folder {folder} was deleted automatically.',
    'Установка завершена, но не удалось удалить папку {folder}.' => 'Installation completed, but failed to delete folder {folder}.',
    'Ошибка:' => 'Error:',
    'Пожалуйста, удалите папку вручную.' => 'Please delete the folder manually.',
    'Установка завершена, но папка {folder} всё ещё существует.' => 'Installation completed, but folder {folder} still exists.',
    'Удалите её вручную для безопасности.' => 'Delete it manually for security.',
    'Если нужны изображения на сайте всех предметов — смотрите папку {folder}.' => 'If you need all item images on the site, look into the {folder} folder.',
    'Удачного вам открытия!' => 'Good luck with your opening!',
    'На сайт' => 'Go to website',

    // =========================================================
    // === Migration messages (browser output) ===
    // =========================================================
    'Ошибка! Файл не найден: {path}' => 'Error! File not found: {path}',
    'Выполняю файл: {file}' => 'Executing file: {file}',
    'Ошибка! Не удалось прочитать файл: {file}' => 'Error! Failed to read file: {file}',
    'Ок. {file} выполнен (mysqli::multi_query).' => 'OK. {file} executed (mysqli::multi_query).',
    'Ошибка! mysqli::multi_query не удался для {file}: {error}' => 'Error! mysqli::multi_query failed for {file}: {error}',
    'Ошибка! при выполнении запроса из {file}: {error}' => 'Error! while executing query from {file}: {error}',
    'Ок. {file} ({count} команд выполнено)' => 'OK. {file} ({count} commands executed)',
    'Откат невозможен — таблицы создавались вручную.' => 'Rollback is impossible — tables were created manually.',
    'Откат невозможен – данные уже исправлены.' => 'Rollback is impossible — data has already been fixed.',

    // =========================================================
    // === Form fields (DbForm and AdminForm) ===
    // =========================================================
    'Host' => 'Host',
    'Port' => 'Port',
    'Database' => 'Database',
    'Username' => 'Username',
    'Password' => 'Password',
    'Login' => 'Login',
    'Email' => 'Email',
];