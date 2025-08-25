<?php
namespace app\l2j;

use yii\db\Connection;
use yii\db\Query;

/**
 * Pwsoft_it
 * ---------------
 * Универсальный драйвер для работы с БД сервера Pwsoft (Yii 2 / PHP 8.2)
 * Всегда подключает кланы и возвращает все нужные поля.
 */
class Pwsoft_it
{
    private Connection $db;
    private string $dbName;

    /**
     * Карта полей (для универсальности под ShopController)
     */
    protected array $fields = [
        'characters' => [
            'account_field' => 'account_name',
            'name_field'    => 'char_name',
            'id_field'      => 'obj_Id',
        ],
        'clans' => [
            'id_field'      => 'clan_id',
            'name_field'    => 'clan_name',
        ],
    ];

    public function __construct(Connection $db, string $dbName = null)
    {
        $this->db = $db;
        // если имя БД передано – добавляем префикс
        $this->dbName = $dbName ? "{$dbName}." : '';
    }

    /* ===== ПОЛУЧЕНИЕ ПОЛЯ ПО КЛЮЧУ ===== */
    public function getField(string $table, string $field): string
    {
        return $this->fields[$table][$field] ?? $field;
    }

    /* ===== ПЕРСОНАЖИ ===== */
    public function charactersQuery(): Query
    {
        return (new Query())
            ->select([
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.title',
                'characters.accesslevel AS access_level',
                'characters.online',
                'characters.onlinetime',
                'characters.race AS base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
            ])
            ->from($this->dbName . 'characters')
            ->leftJoin(
                $this->dbName . 'clan_data',
                $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'characters.clanid'
            );
    }

    /* ===== КЛАНЫ ===== */
    public function clansQuery(): Query
    {
        return (new Query())
            ->select(['clan_data.*'])
            ->from($this->dbName . 'clan_data')
            ->leftJoin(
                $this->dbName . 'characters',
                $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'clan_data.leader_id'
            );
    }

    /* ===== ПРЕДМЕТЫ ===== */
    public function itemsQuery(): Query
    {
        return (new Query())
            ->select([
                'owner_id',
                'object_id',
                'item_id',
                'count',
                'enchant_level',
                'loc',
                'loc_data',
            ])
            ->from($this->dbName . 'items');
    }

    /* ===== СТАТИСТИКА ===== */
    public function getCountAccounts(): int
    {
        return (int)$this->db->createCommand(
            'SELECT COUNT(*) FROM ' . $this->dbName . 'accounts'
        )->queryScalar();
    }

    public function getCountCharacters(): int
    {
        return (int)$this->db->createCommand(
            'SELECT COUNT(*) FROM ' . $this->dbName . 'characters'
        )->queryScalar();
    }

    public function getCountOnlineCharacters(): int
    {
        return (int)$this->db->createCommand(
            'SELECT COUNT(*) FROM ' . $this->dbName . 'characters WHERE online = 1'
        )->queryScalar();
    }

    public function getCountClans(): int
    {
        return (int)$this->db->createCommand(
            'SELECT COUNT(*) FROM ' . $this->dbName . 'clan_data'
        )->queryScalar();
    }

    public function getCountMen(): int
    {
        return (int)$this->db->createCommand(
            'SELECT COUNT(*) FROM ' . $this->dbName . 'characters WHERE sex = 0'
        )->queryScalar();
    }

    public function getCountWomen(): int
    {
        return (int)$this->db->createCommand(
            'SELECT COUNT(*) FROM ' . $this->dbName . 'characters WHERE sex = 1'
        )->queryScalar();
    }

    public function getTopPvp(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->from($this->dbName . 'characters')
            ->where(['>', 'pvpkills', 0])
            ->andWhere(['accesslevel' => 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->from($this->dbName . 'characters')
            ->where(['>', 'pkkills', 0])
            ->andWhere(['accesslevel' => 0])
            ->orderBy(['pkkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTop(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->from($this->dbName . 'characters')
            ->andWhere(['accesslevel' => 0])
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopRich(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->select([
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'COALESCE(SUM(items.count), 0) AS adena_count',
            ])
            ->from($this->dbName . 'characters')
            ->leftJoin(
                $this->dbName . 'items',
                $this->dbName . 'items.owner_id = ' . $this->dbName . 'characters.obj_Id AND ' . $this->dbName . 'items.item_id = 57'
            )
            ->where([$this->dbName . 'characters.accesslevel' => 0])
            ->groupBy($this->dbName . 'characters.obj_Id')
            ->orderBy(['adena_count' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getOnline(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->from($this->dbName . 'characters')
            ->where(['online' => 1])
            ->andWhere(['accesslevel' => 0])
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopClans(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->from($this->dbName . 'clan_data')
            ->orderBy(['reputation_score' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    /* ===== ЗАМКИ / ОСАДЫ ===== */
    public function getCastles(): array
    {
        return (new Query())
            ->select([
                'castle.id',
                'castle.name',
                'castle.taxPercent AS taxPercent',
                'castle.siegeDate AS siegeDate',
                'clan_data.clan_id',
                'clan_data.clan_name',
                'clan_data.leader_id',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
            ])
            ->from($this->dbName . 'castle')
            ->leftJoin(
                $this->dbName . 'clan_data',
                $this->dbName . 'clan_data.hasCastle = ' . $this->dbName . 'castle.id'
            )
            ->all($this->db);
    }

    /* ===== СЛУЖЕБНОЕ ===== */
    public function getChronicle(): string
    {
        return 'it';
    }

    public function getServerName(): string
    {
        return static::class;
    }
}
