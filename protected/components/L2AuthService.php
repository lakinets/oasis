<?php
namespace app\components;

use Yii;
use yii\db\Connection;
use app\models\Ls;

class L2AuthService
{
    /**
     * Подключение к БД логин-сервера (LS), описанного записью Ls
     */
    public static function connectToLs(Ls $ls): Connection
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $ls->db_host, $ls->db_port, $ls->db_name);
        $conn = new Connection([
            'dsn' => $dsn,
            'username' => $ls->db_user,
            'password' => $ls->db_pass ?? '',
            'charset' => 'utf8',
        ]);
        $conn->open();
        return $conn;
    }

    /**
     * Хеш пароля под тип пароля логин-сервера
     * L2j классика обычно: base64( sha1(password) бинарно )
     * Некоторые билды используют whirlpool + base64
     */
    public static function hashForLs(string $password, string $passwordType): string
    {
        $pwd = (string)$password;

        switch ($passwordType) {
            case Ls::PASSWORD_TYPE_WIRLPOOL:
                $digestHex = hash('whirlpool', $pwd);
                return base64_encode(pack('H*', $digestHex));

            case Ls::PASSWORD_TYPE_SHA1:
            default:
                // sha1 по UTF-8 → бинарь → base64
                $digestHex = sha1($pwd);
                return base64_encode(pack('H*', $digestHex));
        }
    }

    public static function findAccount(Connection $lsConn, string $login, string $passwordHash): ?array
    {
        return $lsConn->createCommand('SELECT * FROM accounts WHERE login=:login AND password=:pwd LIMIT 1', [
            ':login' => $login,
            ':pwd'   => $passwordHash,
        ])->queryOne() ?: null;
    }
}
