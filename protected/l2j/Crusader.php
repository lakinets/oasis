<?php
namespace app\l2j;

use yii\db\Connection;
use yii\db\Query;
use yii\db\Expression;

/**
 * Crusader (L2 Scripts / Modern HF+)
 * ---------------
 * Драйвер для работы с БД сервера Crusader (Yii 2 / PHP 8.2)
 */
class Crusader
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
            'name_field' => 'name', // В Crusader название клана в clan_subpledges
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
                'characters.race AS base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'clan_subpledges.name AS clan_name', // Берем имя из сабпледжа
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.crest AS clan_crest', // Тут просто crest
            ])
            ->from($this->dbName . 'characters')
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
                'clan_data.reputation_score',
                'clan_data.crest AS clan_crest',
                'clan_data.ally_id',
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
     * В Crusader предметы выдаются через эту таблицу.
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
        return $this->clansQuery()
            ->orderBy(['reputation_score' => SORT_DESC])
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
                'castle.tax AS taxPercent',
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
                $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'castle.owner_id'
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
            ->from($this->dbName . 'premium_accounts')
            ->where(['account' => $accountName])
            ->limit(1)
            ->one($this->db);

        // Если expire_time хранится как дата (Y-m-d H:i:s) или timestamp, возвращаем как есть
        return [
            'dateEnd' => ($res && isset($res['expire_time']) && $res['expire_time'] > 0 ? $res['expire_time'] : 0),
        ];
    }

    public function addPremium(string $accountName, int $timeEnd): bool
    {
        $exists = (new Query())
            ->from($this->dbName . 'premium_accounts')
            ->where(['account' => $accountName])
            ->exists($this->db);

        if ($exists) {
            return $this->db->createCommand()
                ->update(
                    $this->dbName . 'premium_accounts',
                    ['expire_time' => $timeEnd],
                    ['account' => $accountName]
                )->execute() > 0;
        } else {
            return $this->db->createCommand()
                ->insert($this->dbName . 'premium_accounts', [
                    'account'     => $accountName,
                    'expire_time' => $timeEnd,
                ])->execute() > 0;
        }
    }

    public function removeHWID(string $accountName): bool
    {
        // В Crusader есть поле allow_hwid в accounts
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
        return 'crusader'; // Или 'hf' в зависимости от логики сайта
    }

    public function getServerName(): string
    {
        return static::class;
    }
}