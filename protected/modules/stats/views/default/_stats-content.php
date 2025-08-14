<?php
use yii\helpers\Html;

if (!$gs) {
    echo '<div class="alert alert-info">' . \Yii::t('main', 'Сервер не выбран.') . '</div>';
    return;
}

switch ($type) {
    case 'pvp':
        $title = 'Топ PVP';
        break;
    case 'pk':
        $title = 'Топ PK';
        break;
    case 'clans':
        $title = 'Топ Clans';
        break;
    default:
        $title = 'Статистика';
}

echo "<h2>{$title}</h2>";
echo "<p>Данные по {$type} на сервере {$gs->name}</p>";

// Здесь должна быть логика получения данных из игрового сервера
// Например, через API или подключение к базе данных сервера