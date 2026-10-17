<?php
namespace app\l2j;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use app\models\AllItems;

/**
 * PainTeamIt (Interlude)
 * ---------------
 * Драйвер для работы с БД сервера PainTeam (Yii 2 / PHP 8.2)
 */
class PainTeamIt
{
    private Connection $db;
    private string $dbName;

    /**
     * Поля из БД
     * @var array
     */
    protected array $fields = [
        'accounts.access_level'   => 'accounts.access_level',
        'characters.access_level' => 'characters.accesslevel',
        'characters.char_id'      => 'characters.obj_Id',
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
        return $this->db->createCommand()
            ->insert($this->dbName . 'accounts', [
                'login'        => $login,
                'password'     => $password, // Пароль должен быть уже зашифрован!
                'access_level' => $access_level,
            ])->execute() > 0;
    }

    public function accountsQuery(): Query
    {
        return (new Query())
            ->select(['login', 'password', 'last_access AS last_active', 'access_level'])
            ->from($this->dbName . 'accounts');
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
                'characters.pvpkills', 'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.title',
                'characters.online',
                'characters.onlinetime',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
                'clan_data.clan_name',
                // Специфичный подзапрос для PainTeam (проверка тюрьмы)
                '(SELECT IF(value>0,1,0) FROM ' . $this->dbName . 'character_variables WHERE character_variables.obj_Id = characters.obj_Id AND character_variables.name = "jail" LIMIT 1) AS jail',
                'characters.accesslevel AS access_level',
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'character_subclasses.curHp', 'character_subclasses.curMp', 'character_subclasses.curCp',
                'character_subclasses.maxHp', 'character_subclasses.maxMp', 'character_subclasses.maxCp'
            ])
            ->from($this->dbName . 'characters')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'characters.clanid = ' . $this->dbName . 'clan_data.clan_id')
            ->leftJoin($this->dbName . 'character_subclasses', $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'character_subclasses.char_obj_id AND ' . $this->dbName . 'character_subclasses.isBase = 1');
    }

    /* ---------- КЛАНЫ ---------- */

    public function clansQuery(): Query
    {
        return (new Query())
            ->select([
                'clan_data.clan_id',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.ally_id',
                'clan_data.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'clan_data.reputation_score',
                'characters.char_name',
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'characters.karma',
                'characters.pvpkills', 'characters.pkkills',
                'characters.title',
                'characters.online', 'characters.onlinetime',
                '(SELECT COUNT(0) FROM ' . $this->dbName . 'characters WHERE clanid = clan_data.clan_id) AS ccount',
                'ally_data.ally_name',
                'ally_data.crest AS ally_crest',
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'character_subclasses.curHp', 'character_subclasses.curMp', 'character_subclasses.curCp',
                'character_subclasses.maxHp', 'character_subclasses.maxMp', 'character_subclasses.maxCp'
            ])
            ->from($this->dbName . 'clan_data')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'clan_data.leader_id = ' . $this->dbName . 'characters.obj_Id')
            ->leftJoin($this->dbName . 'ally_data', $this->dbName . 'clan_data.ally_id = ' . $this->dbName . 'ally_data.ally_id')
            ->leftJoin($this->dbName . 'character_subclasses', $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'character_subclasses.char_obj_id AND ' . $this->dbName . 'character_subclasses.isBase = 1');
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
     * В PainTeam используется таблица character_donate!
     */
    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): void
    {
        $this->db->createCommand()
            ->insert($this->dbName . 'character_donate', [
                'obj_Id'    => $ownerId,
                'char_name' => 'ghtweb',
                'item_id'   => $itemId,
                'count'     => $count,
                // 'enchant_level' => $enchantLevel, // В оригинальном коде закомментировано
            ])->execute();
    }

    /**
     * Добавление множества предметов в игру
     */
    public function multiInsertItem(array $items): void
    {
        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'obj_Id'      => $item['owner_id'],
                'char_name'   => 'ghtweb',
                'item_id'     => $item['item_id'],
                'count'       => $item['count'] ?? 1,
                'description' => 'GHTWEB',
                'issued'      => 0,
            ];
        }

        if (!empty($rows)) {
            $this->db->createCommand()
                ->batchInsert($this->dbName . 'character_donate', [
                    'obj_Id', 'char_name', 'item_id', 'count', 'description', 'issued'
                ], $rows)
                ->execute();
        }
    }

    /* ---------- СТАТИСТИКА (РАСЫ, ОНЛАЙН) ---------- */

    private function getCountRacesById(int $id): int
    {
        $data = Yii::$app->params['l2']['classList'] ?? [];
        $races = [];
        foreach ($data as $k => $v) {
            $races[$v['race']][] = $k;
        }

        if (empty($races[$id])) {
            return 0;
        }

        return (int)(new Query())
            ->from($this->dbName . 'character_subclasses')
            ->where(['class_id' => $races[$id], 'isBase' => 1])
            ->count('*', $this->db);
    }

    public function getCountRaceHuman(): int { return $this->getCountRacesById(0); }
    public function getCountRaceElf(): int { return $this->getCountRacesById(1); }
    public function getCountRaceDarkElf(): int { return $this->getCountRacesById(2); }
    public function getCountRaceOrk(): int { return $this->getCountRacesById(3); }
    public function getCountRaceDwarf(): int { return $this->getCountRacesById(4); }
    public function getCountRaceKamael(): int { return $this->getCountRacesById(5); }

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
            ->leftJoin($this->dbName . 'items', $this->dbName . 'items.owner_id = ' . $this->dbName . 'characters.obj_Id')
            ->where(['items.item_id' => 57, 'characters.accesslevel' => 0])
            ->groupBy([$this->dbName . 'characters.obj_Id'])
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
            ->andWhere(['characters.accesslevel' => 0])
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
                'castle.tax_percent AS taxPercent',
                'castle.siege_date AS siegeDate',
                'clan_data.clan_id',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.ally_id',
                'clan_data.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_large_crest',
                'ally_data.ally_name',
                'ally_data.crest AS ally_crest'
            ])
            ->from($this->dbName . 'castle')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'clan_data.hasCastle = ' . $this->dbName . 'castle.id')
            ->leftJoin($this->dbName . 'ally_data', $this->dbName . 'clan_data.ally_id = ' . $this->dbName . 'ally_data.ally_id')
            ->all($this->db);
    }

    public function getSiege(): array
    {
        return (new Query())
            ->select([
                'siege_clans.residence_id AS castle_id',
                'siege_clans.clan_id',
                'siege_clans.type',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.ally_id',
                'clan_data.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'clan_data.crest AS ally_crest',
                'ally_data.ally_name'
            ])
            ->from($this->dbName . 'siege_clans')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'siege_clans.clan_id = ' . $this->dbName . 'clan_data.clan_id')
            ->leftJoin($this->dbName . 'ally_data', $this->dbName . 'ally_data.ally_id = ' . $this->dbName . 'clan_data.ally_id')
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
            'dateEnd' => ($res && $res['bonus_expire'] > 0 ? substr($res['bonus_expire'], 0, 10) : 0),
        ];
    }

    public function addPremium(string $accountName, int $timeEnd, bool $isIssetPremium = false): bool
    {
        return $this->db->createCommand()
            ->update(
                $this->dbName . 'accounts',
                ['bonus_expire' => $timeEnd],
                ['login' => $accountName]
            )->execute() > 0;
    }

    public function removeHWID(string $accountName): bool
    {
        return $this->db->createCommand()
            ->update(
                $this->dbName . 'accounts',
                ['LockHWID' => null, 'LockHWIDSecond' => null],
                ['login' => $accountName]
            )->execute() > 0;
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
                'characters.obj_Id AS char_id', 'characters.account_name', 'characters.char_name',
                'characters.pvpkills', 'characters.pkkills', 'characters.clanid AS clan_id',
                'characters.title', 'characters.online', 'characters.onlinetime',
                'clan_data.clan_name', 'clan_data.clan_level', 'clan_data.hasCastle',
                'clan_data.crest AS clan_crest', 'clan_data.reputation_score',
                'character_subclasses.class_id AS base_class', 'character_subclasses.level',
                'character_subclasses.exp', 'character_subclasses.sp',
                'character_subclasses.curHp', 'character_subclasses.curMp', 'character_subclasses.curCp',
                'character_subclasses.maxHp', 'character_subclasses.maxMp', 'character_subclasses.maxCp'
            ])
            ->from($this->dbName . 'items')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'items.owner_id = ' . $this->dbName . 'characters.obj_Id')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'characters.clanid = ' . $this->dbName . 'clan_data.clan_id')
            ->leftJoin($this->dbName . 'character_subclasses', $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'character_subclasses.char_obj_id AND ' . $this->dbName . 'character_subclasses.isBase = 1')
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
        return 'interlude';
    }

    public function getServerName(): string
    {
        return static::class;
    }
}