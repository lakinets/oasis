<?php
namespace app\l2j;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use app\models\AllItems;

/**
 * OpenTeam_hf (High Five)
 * ---------------
 * Драйвер для работы с БД сервера OpenTeam (Yii 2 / PHP 8.2)
 */
class OpenTeam_hf
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
            ->select(['login', 'password', 'lastactive AS last_active', 'access_level'])
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
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp', 'character_subclasses.sp',
                'character_subclasses.maxHp', 'character_subclasses.maxMp', 'character_subclasses.maxCp',
                'character_subclasses.curHp', 'character_subclasses.curMp', 'character_subclasses.curCp',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFortress AS hasFort',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
                'clan_data.clan_name',
                'characters.accesslevel AS access_level',
                // Проверка тюрьмы (jail)
                '(SELECT IF(value>0,1,0) FROM ' . $this->dbName . 'character_variables WHERE character_variables.obj_id = characters.obj_Id AND character_variables.name = "jailed" LIMIT 1) as jail'
            ])
            ->from($this->dbName . 'characters')
            ->leftJoin($this->dbName . 'character_subclasses', $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'character_subclasses.char_obj_id AND ' . $this->dbName . 'character_subclasses.isBase = 1')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'characters.clanid = ' . $this->dbName . 'clan_data.clan_id');
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
                'clan_data.hasFortress',
                'clan_data.ally_id',
                'ally_data.ally_name',
                'clan_data.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'ally_data.crest AS ally_crest',
                'clan_data.reputation_score',
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'character_subclasses.level',
                'character_subclasses.maxHp', 'character_subclasses.curHp',
                'character_subclasses.maxCp', 'character_subclasses.curCp',
                'character_subclasses.maxMp', 'character_subclasses.curMp',
                'characters.sex', 'characters.x', 'characters.y', 'characters.z',
                'character_subclasses.exp', 'character_subclasses.sp',
                'characters.karma', 'characters.pvpkills', 'characters.pkkills',
                'character_subclasses.class_id AS base_class',
                'characters.title', 'characters.online', 'characters.onlinetime',
                '(SELECT COUNT(0) FROM ' . $this->dbName . 'characters WHERE characters.clanid = clan_data.clan_id) as ccount'
            ])
            ->from($this->dbName . 'clan_data')
            ->leftJoin($this->dbName . 'ally_data', $this->dbName . 'clan_data.ally_id = ' . $this->dbName . 'ally_data.ally_id')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'clan_data.leader_id = ' . $this->dbName . 'characters.obj_Id')
            ->leftJoin($this->dbName . 'character_subclasses', $this->dbName . 'characters.obj_Id = ' . $this->dbName . 'character_subclasses.char_obj_id')
            ->andWhere(['character_subclasses.isBase' => 1]);
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
     * В OpenTeam_hf используется таблица items_delayed!
     */
    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): void
    {
        $this->db->createCommand()
            ->insert($this->dbName . 'items_delayed', [
                'owner_id'      => $ownerId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'description'   => 'GHTWEB',
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
                'owner_id'      => $item['owner_id'],
                'item_id'       => $item['item_id'],
                'count'         => $item['count'] ?? 1,
                'enchant_level' => $item['enchant'] ?? 0,
                'description'   => 'GHTWEB',
            ];
        }

        if (!empty($rows)) {
            $this->db->createCommand()
                ->batchInsert($this->dbName . 'items_delayed', [
                    'owner_id', 'item_id', 'count', 'enchant_level', 'description'
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
                'castle.taxPercent AS taxPercent',
                'castle.siegeDate AS siegeDate',
                'clan_data.clan_id',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.hasFortress AS hasFort',
                'clan_subpledges.name AS clan_name',
                'clan_data.ally_id',
                'ally_data.ally_name',
                'clan_subpledges.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'ally_data.crest AS ally_crest'
            ])
            ->from($this->dbName . 'castle')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'clan_data.hasCastle = ' . $this->dbName . 'castle.id')
            ->leftJoin($this->dbName . 'clan_subpledges', $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'clan_subpledges.clan_id AND ' . $this->dbName . 'clan_subpledges.type = 0')
            ->leftJoin($this->dbName . 'ally_data', $this->dbName . 'clan_data.ally_id = ' . $this->dbName . 'ally_data.ally_id')
            ->all($this->db);
    }

    public function getSiege(): array
    {
        return (new Query())
            ->select([
                // ИСПРАВЛЕНО: убран двойной AS (было unit_id AS residence_id AS castle_id)
                'siege_clans.unit_id AS castle_id',
                'siege_clans.clan_id',
                'siege_clans.type',
                'clan_subpledges.name AS clan_name',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.hasFortress AS hasFort',
                'clan_data.ally_id',
                'ally_data.ally_name',
                'clan_subpledges.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'ally_data.crest AS ally_crest'
            ])
            ->from($this->dbName . 'siege_clans')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'siege_clans.clan_id = ' . $this->dbName . 'clan_data.clan_id')
            ->leftJoin($this->dbName . 'clan_subpledges', $this->dbName . 'clan_data.clan_id = ' . $this->dbName . 'clan_subpledges.clan_id AND ' . $this->dbName . 'clan_subpledges.type = 0')
            ->leftJoin($this->dbName . 'ally_data', $this->dbName . 'clan_data.ally_id = ' . $this->dbName . 'ally_data.ally_id')
            ->where(['clan_subpledges.type' => 0])
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

    public function addPremium(string $accountName, int $timeEnd): bool
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
        // @TODO: пока непонятно где в БД идёт привязка
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
                'characters.obj_Id AS char_id', 'characters.account_name', 'characters.char_name',
                'characters.x', 'characters.y', 'characters.z',
                'characters.pvpkills', 'characters.pkkills', 'characters.clanid AS clan_id',
                'characters.title', 'characters.online', 'characters.onlinetime',
                'clan_data.clan_name', 'clan_data.clan_level', 'clan_data.hasFortress AS hasFort',
                'clan_data.hasCastle', 'clan_data.crest AS clan_crest', 'clan_data.reputation_score',
                'character_subclasses.class_id as base_class', 'character_subclasses.level',
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
        return 'hf';
    }

    public function getServerName(): string
    {
        return static::class;
    }
}