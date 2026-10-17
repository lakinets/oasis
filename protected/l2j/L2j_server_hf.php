<?php
namespace app\l2j;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use app\models\AllItems;

/**
 * L2j_server_hf (High Five)
 * ---------------
 * Драйвер для работы с БД сервера L2J Server (Yii 2 / PHP 8.2)
 */
class L2j_server_hf
{
    private Connection $db;
    private string $dbName;

    /**
     * Поля из БД
     * @var array
     */
    protected array $fields = [
        'accounts.access_level'   => 'accounts.accessLevel',
        'characters.access_level' => 'characters.accesslevel',
        'characters.char_id'      => 'characters.charId',
        'clan_data.clan_id'       => 'clan_data.clan_id',
    ];

    public function __construct(Connection $db, string $dbName = null)
    {
        $this->db = $db;
        $this->dbName = $dbName ? "{$dbName}." : '';
    }

    public function getField(string $fieldName): ?string
    {
        return $this->fields[$fieldName] ?? null;
    }

    /* ---------- АККАУНТЫ ---------- */

    public function insertAccount(string $login, string $password, int $access_level = 0): bool
    {
        $this->db->createCommand()->insert($this->dbName . 'accounts', [
            'login'       => $login,
            'password'    => $password, // Должен быть уже зашифрован
            'accessLevel' => $access_level,
        ])->execute();

        $this->db->createCommand()->insert($this->dbName . 'account_premium', [
            'account_name'    => $login,
            'premium_service' => 0,
            'enddate'         => 0,
        ])->execute();

        return true;
    }

    public function accountsQuery(): Query
    {
        return (new Query())
            ->select(['login', 'password', 'lastactive AS last_active', 'accessLevel AS access_level'])
            ->from($this->dbName . 'accounts');
    }

    /* ---------- ПЕРСОНАЖИ ---------- */

    public function charactersQuery(): Query
    {
        return (new Query())
            ->select([
                'characters.account_name',
                'characters.charId AS char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.karma',
                'characters.pvpkills', 'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.title',
                'characters.online',
                'characters.onlinetime',
                'characters.base_class',
                'characters.level',
                'characters.exp', 'characters.sp',
                'characters.maxHp', 'characters.maxMp', 'characters.maxCp',
                'characters.curHp', 'characters.curMp', 'characters.curCp',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
                'clan_data.clan_name',
                'characters.accesslevel AS access_level',
                '(SELECT IF(val>0,1,0) FROM ' . $this->dbName . 'character_variables WHERE character_variables.charId = characters.charId AND character_variables.var = "jailed" LIMIT 1) AS jail',
                'fort.id AS hasFort'
            ])
            ->from($this->dbName . 'characters')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'characters.clanid = ' . $this->dbName . 'clan_data.clan_id')
            ->leftJoin($this->dbName . 'fort', $this->dbName . 'characters.charId = ' . $this->dbName . 'fort.owner');
    }

    /* ---------- КЛАНЫ ---------- */

    public function clansQuery(): Query
    {
        return (new Query())
            ->select([
                'clan_data.clan_id', 'clan_data.clan_name', 'clan_data.clan_level',
                'clan_data.hasCastle', 'clan_data.ally_id', 'clan_data.ally_name',
                'clan_data.leader_id',
                'clan_data.crest_id AS clan_crest',
                'clan_data.crest_large_id AS clan_crest_large',
                'clan_data.ally_crest_id AS ally_crest',
                'clan_data.reputation_score',
                'characters.char_name', 'characters.account_name', 'characters.charId AS char_id',
                'characters.level', 'characters.curHp', 'characters.curMp', 'characters.curCp',
                'characters.maxHp', 'characters.maxMp', 'characters.maxCp',
                'characters.sex', 'characters.x', 'characters.y', 'characters.z',
                'characters.exp', 'characters.sp',
                'characters.karma', 'characters.pvpkills', 'characters.pkkills',
                'characters.classid AS base_class',
                'characters.title', 'characters.online', 'characters.onlinetime',
                'characters.accesslevel AS access_level',
                '(SELECT COUNT(0) FROM ' . $this->dbName . 'characters WHERE clanid = clan_data.clan_id) as ccount',
                'fort.id AS hasFort'
            ])
            ->from($this->dbName . 'clan_data')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'clan_data.leader_id = ' . $this->dbName . 'characters.charId')
            ->leftJoin($this->dbName . 'fort', $this->dbName . 'characters.charId = ' . $this->dbName . 'fort.owner');
    }

    /* ---------- ПРЕДМЕТЫ ---------- */

    public function itemsQuery(): Query
    {
        return (new Query())
            ->select(['owner_id', 'object_id', 'item_id', 'count', 'enchant_level', 'loc', 'loc_data'])
            ->from($this->dbName . 'items');
    }

    /**
     * Добавление предмета в игру.
     * В этом драйвере предмет добавляется напрямую в items с генерацией object_id.
     */
    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): void
    {
        $maxId = (int)$this->db->createCommand("SELECT MAX(object_id)+1 FROM {$this->dbName}items")->queryScalar();

        $this->db->createCommand()
            ->insert($this->dbName . 'items', [
                'owner_id'      => $ownerId,
                'object_id'     => $maxId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
            ])->execute();
    }

    /**
     * Добавление множества предметов в игру
     */
    public function multiInsertItem(array $items): void
    {
        foreach ($items as $item) {
            $this->insertItem(
                $item['owner_id'],
                $item['item_id'],
                $item['count'] ?? 1,
                $item['enchant'] ?? 0
            );
        }
    }

    /* ---------- СТАТИСТИКА (РАСЫ, ОНЛАЙН) ---------- */

    public function getCountRaceHuman(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['race' => 0])->count('*', $this->db);
    }

    public function getCountRaceElf(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['race' => 1])->count('*', $this->db);
    }

    public function getCountRaceDarkElf(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['race' => 2])->count('*', $this->db);
    }

    public function getCountRaceOrk(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['race' => 3])->count('*', $this->db);
    }

    public function getCountRaceDwarf(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['race' => 4])->count('*', $this->db);
    }

    public function getCountRaceKamael(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['race' => 5])->count('*', $this->db);
    }

    public function getCountAccounts(): int
    {
        return (int)(new Query())->from($this->dbName . 'accounts')->count('*', $this->db);
    }

    public function getCountCharacters(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->count('*', $this->db);
    }

    public function getCountOnlineCharacters(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['online' => 1])->count('*', $this->db);
    }

    public function getCountClans(): int
    {
        return (int)(new Query())->from($this->dbName . 'clan_data')->count('*', $this->db);
    }

    public function getCountMen(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['sex' => 0])->count('*', $this->db);
    }

    public function getCountWomen(): int
    {
        return (int)(new Query())->from($this->dbName . 'characters')->where(['sex' => 1])->count('*', $this->db);
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
            ->where(['characters.accesslevel' => 0])
            ->orderBy(['exp' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopRich(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->addSelect(['SUM(items.count) AS adena_count'])
            ->leftJoin($this->dbName . 'items', $this->dbName . 'items.owner_id = ' . $this->dbName . 'characters.charId')
            ->where(['items.item_id' => 57, 'characters.accesslevel' => 0])
            ->groupBy([$this->dbName . 'characters.charId'])
            ->orderBy(['adena_count' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getOnline(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->where(['online' => 1, 'characters.accesslevel' => 0])
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopClans(int $limit = 20, int $offset = 0): array
    {
        return $this->clansQuery()
            ->where(['characters.accesslevel' => 0])
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
                'castle.taxPercent',
                'castle.siegeDate',
                'clan_data.clan_id',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.clan_name',
                'clan_data.ally_id',
                'clan_data.ally_name',
                'clan_data.leader_id',
                'clan_data.crest_id AS clan_crest',
                'clan_data.crest_large_id AS clan_crest_large',
                'clan_data.ally_crest_id AS ally_crest'
            ])
            ->from($this->dbName . 'castle')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'clan_data.hasCastle = ' . $this->dbName . 'castle.id')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'clan_data.leader_id = ' . $this->dbName . 'characters.charId')
            ->all($this->db);
    }

    public function getSiege(): array
    {
        return (new Query())
            ->select([
                'siege_clans.castle_id',
                'siege_clans.clan_id',
                'siege_clans.type',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.ally_id',
                'clan_data.ally_name',
                'clan_data.leader_id',
                'clan_data.crest_id AS clan_crest',
                'clan_data.crest_large_id AS clan_crest_large',
                'clan_data.ally_crest_id AS ally_crest'
            ])
            ->from($this->dbName . 'siege_clans')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'siege_clans.clan_id = ' . $this->dbName . 'clan_data.clan_id')
            ->all($this->db);
    }

    /* ---------- ПРЕМИУМ И HWID ---------- */

    public function getPremiumInfo(string $accountName): array
    {
        $res = (new Query())
            ->from($this->dbName . 'account_premium')
            ->where(['account_name' => $accountName, 'premium_service' => 1])
            ->limit(1)
            ->one($this->db);

        return [
            'dateEnd' => (isset($res['enddate']) && is_numeric($res['enddate']) ? substr($res['enddate'], 0, 10) : 0),
        ];
    }

    public function addPremium(string $accountName, int $timeEnd): bool
    {
        return $this->db->createCommand()
            ->update(
                $this->dbName . 'account_premium',
                ['enddate' => $timeEnd, 'premium_service' => 1],
                ['account_name' => $accountName]
            )->execute() > 0;
    }

    public function removeHWID(string $accountName): bool
    {
        // @TODO, пока непонятно где в БД идёт привязка
        return true;
    }

    /* ---------- КОНТРОЛЬ ПРЕДМЕТОВ ---------- */

    public function getItemsControl(array $itemsIds): array
    {
        if (!$itemsIds) {
            return [];
        }

        $resAllItems = AllItems::find()->where(['item_id' => $itemsIds])->all();
        $itemNames = [];
        foreach ($resAllItems as $row) {
            $itemNames[$row->item_id] = $row;
        }

        $res = (new Query())
            ->select([
                'MAX(items.count) AS maxCountItems',
                'COUNT(items.count) AS countItems',
                'items.owner_id', 'items.object_id', 'items.item_id', 'items.count', 'items.enchant_level', 'items.loc', 'items.loc_data',
                'characters.charId AS char_id', 'characters.account_name', 'characters.char_name',
                'characters.x', 'characters.y', 'characters.z',
                'characters.pvpkills', 'characters.pkkills', 'characters.clanid AS clan_id',
                'characters.title', 'characters.online', 'characters.onlinetime',
                'clan_data.clan_level', 'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest', 'clan_data.reputation_score',
                'characters.classid AS base_class', 'characters.level',
                'characters.exp', 'characters.sp',
                'characters.curHp', 'characters.curMp', 'characters.curCp',
                'characters.maxHp', 'characters.maxMp', 'characters.maxCp',
                'clan_data.clan_name'
            ])
            ->from($this->dbName . 'items')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'items.owner_id = ' . $this->dbName . 'characters.charId')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'characters.clanid = ' . $this->dbName . 'clan_data.clan_id')
            ->where(['in', 'items.item_id', $itemsIds])
            ->groupBy(['items.owner_id', 'items.item_id'])
            ->orderBy(['maxCountItems' => SORT_DESC])
            ->all($this->db);

        $characters = [];

        foreach ($res as $item) {
            if (!isset($characters[$item['item_id']]['maxTotalItems'])) {
                $characters[$item['item_id']]['maxTotalItems'] = 0;
            }

            $characters[$item['item_id']]['itemInfo'] = $itemNames[$item['item_id']] ?? null;
            $characters[$item['item_id']]['characters'][] = $item;
            $characters[$item['item_id']]['maxTotalItems'] += $item['maxCountItems'];
            $characters[$item['item_id']]['totalItems'] = count($characters[$item['item_id']]['characters']);
        }

        foreach (array_diff_key($itemNames, $characters) as $item) {
            $characters[$item->item_id]['itemInfo'] = $item;
            $characters[$item->item_id]['characters'] = [];
            $characters[$item->item_id]['maxTotalItems'] = 0;
            $characters[$item->item_id]['totalItems'] = 0;
        }

        return $characters;
    }

    /* ---------- СЛУЖЕБНЫЕ ---------- */

    public function getChronicle(): string
    {
        return 'hf';
    }

    public function getServerName(): string
    {
        return static::class;
    }
}