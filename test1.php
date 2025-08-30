<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=neolferon;charset=utf8mb4';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $online = (int)$pdo->query('SELECT COUNT(*) FROM characters WHERE online = 1')->fetchColumn();
echo $pdo->query('SELECT COUNT(*) FROM castle')->fetchColumn();
}