<?php
namespace app\l2j;

use yii\db\Connection;
use yii\db\Query;

class Lucera2
{
    private Connection $db;
	
	public function loginExists(string $login): bool
{
    return (bool)$this->db->createCommand('SELECT COUNT(*) FROM accounts WHERE login = :login')
        ->bindValue(':login', $login)
        ->queryScalar();
}

public function createAccount(string $login, string $password): void
{
    $this->db->createCommand()->insert('accounts', [
        'login'    => $login,
        'password' => sha1($password), // или md5/bcrypt/соль и т.д.
    ])->execute();
}

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getCountAccounts(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM accounts')->queryScalar();
    }

    public function getCountCharacters(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters')->queryScalar();
    }

    public function getCountOnlineCharacters(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE online = 1')->queryScalar();
    }

    public function getCountClans(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM clan_data')->queryScalar();
    }

    public function getCountMen(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE sex = 0')->queryScalar();
    }

    public function getCountWomen(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE sex = 1')->queryScalar();
    }

    public function getTopPvp(int $limit = 20): array
    {
        return (new Query())
            ->from('characters')
            ->where(['>', 'pvpkills', 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->all($this->db);
    }

    public function getTopPk(int $limit = 20): array
    {
        return (new Query())
            ->from('characters')
            ->where(['>', 'pkkills', 0])
            ->orderBy(['pkkills' => SORT_DESC])
            ->limit($limit)
            ->all($this->db);
    }

    public function getTop(int $limit = 20): array
    {
        return (new Query())
            ->from('characters')
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->all($this->db);
    }

    public function getTopClans(int $limit = 20): array
    {
        return (new Query())
            ->from('clan_data')
            ->orderBy(['reputation_score' => SORT_DESC])
            ->limit($limit)
            ->all($this->db);
    }

    public function getCastles(): array
    {
        return (new Query())
            ->from('castle')
            ->all($this->db);
    }

    public function getOnline(int $limit = 20): array
    {
        return (new Query())
            ->from('characters')
            ->where(['online' => 1])
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->all($this->db);
    }

    public function getTopRich(int $limit = 20): array
    {
        return (new Query())
            ->select([
                'c.charId as char_id',
                'c.char_name',
                'SUM(i.count) as adena_count',
            ])
            ->from('characters c')
            ->leftJoin('items i', 'i.owner_id = c.charId AND i.item_id = 57')
            ->groupBy('c.charId')
            ->orderBy(['adena_count' => SORT_DESC])
            ->limit($limit)
            ->all($this->db);
    }
}