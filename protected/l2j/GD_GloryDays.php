<?php
namespace app\l2j;

use yii\db\Connection;
use yii\db\Query;

/**
 * GloryDays (Goddess of Destruction)
 * ---------------
 * Драйвер для работы с БД сервера Glory Days (Yii 2 / PHP 8.2)
 */
class GloryDays
{
    private Connection $db;
    private string $dbName;

    protected array $fields = [
        'characters' => [
            'account_field' => 'account_name',
            'name_field'    => 'char_name',
            'id_field'      => 'obj_Id',
        ],
        'clans' => [
            'id_field'   => 'clan_id',
            'name_field' => 'name', // В Glory Days название клана в clan_subpledges
        ],
    ];

    public function __construct(Connection $db, string $dbName = null)
    {
        $this->db = $db;
        $this->dbName = $dbName ? "{$dbName}." : '';
    }

    public function getField(string $table, string $field): string
    {
        return $this->fields[$table][$field] ?? $field;
    }

    /* ---------- ПЕРСОНАЖИ ---------- */
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
                
                // Характеристики из сабклассов
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'character_subclasses.maxHp', 'character_subclasses.curHp',
                'character_subclasses.maxCp', 'character_subclasses.curCp',
                'character_subclasses.maxMp', 'character_subclasses.curMp',

                'clan_subpledges.name AS clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
                
                // Проверка тюрьмы
                '(SELECT IF(value>0,1,0) FROM ' . $this->dbName . 'character_variables WHERE obj_id = characters.obj_Id AND name = "jail" LIMIT 1) as jail'
            ])
            ->from($this->dbName . 'characters')
            ->leftJoin(
                $this->dbName . 'character_subclasses',
                $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'character_subclasses.char_obj_id AND ' . $this->dbName . 'character_subclasses.active = 1'
            )
            ->leftJoin(
                $this->dbName . 'clan_data',
                $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'characters.clanid'
            )
            ->leftJoin(
                $this->dbName . 'clan_subpledges',
                $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'clan_subpledges.clan_id AND ' . $this->dbName . 'clan_subpledges.type = 0'
            );
    }

    /* ---------- КЛАНЫ ---------- */
    public function clansQuery(): Query
    {
        return (new Query())
            ->select([
                'clan_data.clan_id',
                'clan_subpledges.name AS clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFortress',
                'clan_data.crest AS clan_crest',
                'clan_data.ally_id',
                'clan_data.reputation_score',
                'clan_subpledges.leader_id',
            ])
            ->from($this->dbName . 'clan_data')
            ->leftJoin(
                $this->dbName . 'clan_subpledges',
                $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'clan_subpledges.clan_id AND ' . $this->dbName . 'clan_subpledges.type = 0'
            );
    }

    /* ---------- ПРЕДМЕТЫ ---------- */
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

    /**
     * Добавляет предмет в отложенную очередь выдачи (items_delayed).
     */
    public function insertItem(int $charId, int $itemId, int $count, int $enchant): void
    {
        $this->db->createCommand()
            ->insert($this->dbName . 'items_delayed', [
                'owner_id'      => $charId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchant,
                'description'   => 'GHTWEB',
                'payment_status'=> 0,
            ])
            ->execute();
    }

    /* ---------- СТАТИСТИКА ---------- */
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

    /* ---------- ТОПЫ ---------- */
    public function getTopPvp(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->where(['>', 'pvpkills', 0])
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->where(['>', 'pkkills', 0])
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['pkkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTop(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['character_subclasses.exp' => SORT_DESC])
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
                $this->dbName . 'items.owner_id = ' . $this->dbName . 'characters.obj_Id AND ' .
                $this->dbName . 'items.item_id = 57'
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
        return $this->charactersQuery()
            ->where(['characters.online' => 1])
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['character_subclasses.level' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopClans(int $limit = 20, int $offset = 0): array
    {
        return $this->clansQuery()
            ->orderBy(['clan_data.reputation_score' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    /* ---------- ЗАМКИ / ОСАДЫ ---------- */
    public function getCastles(): array
    {
        return (new Query())
            ->select([
                'castle.id',
                'castle.name',
                'castle.tax_percent AS taxPercent',
                'castle.siege_date AS siegeDate',
                'clan_data.clan_id',
                'clan_subpledges.name AS clan_name',
                'clan_subpledges.leader_id',
                'clan_data.clan_level',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
            ])
            ->from($this->dbName . 'castle')
            ->leftJoin(
                $this->dbName . 'clan_data',
                $this->dbName . 'clan_data.hasCastle = ' . $this->dbName . 'castle.id'
            )
            ->leftJoin(
                $this->dbName . 'clan_subpledges',
                $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'clan_subpledges.clan_id AND ' . $this->dbName . 'clan_subpledges.type = 0'
            )
            ->all($this->db);
    }

    /* ---------- ПРЕМИУМ И HWID ---------- */
    
    public function getPremiumInfo(string $accountName): array
    {
        $res = (new Query())
            ->from($this->dbName . 'accounts')
            ->where(['login' => $accountName])
            ->andWhere(['>', 'bonus_expire', 0])
            ->limit(1)
            ->one($this->db);

        return [
            'dateEnd' => ($res && isset($res['bonus_expire']) && $res['bonus_expire'] > 0 ? substr($res['bonus_expire'], 0, 10) : 0),
        ];
    }

    public function addPremium(string $accountName, int $timeEnd): bool
    {
        return $this->db->createCommand()
            ->update(
                $this->dbName . 'accounts',
                ['bonus_expire' => $timeEnd, 'bonus' => 1],
                ['login' => $accountName]
            )->execute() > 0;
    }

    public function removeHWID(string $accountName): bool
    {
        return $this->db->createCommand()
            ->update(
                $this->dbName . 'accounts',
                ['allow_hwid' => '*'],
                ['login' => $accountName]
            )->execute() > 0;
    }

    /* ---------- СЛУЖЕБНОЕ ---------- */
    public function getChronicle(): string
    {
        return 'gd'; 
    }

    public function getServerName(): string
    {
        return static::class;
    }
}