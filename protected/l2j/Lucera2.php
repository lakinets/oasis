<?php
namespace app\l2j;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use app\models\AllItems;

/**
 * Lucera2 (Version >= 8.3)
 * ---------------
 * Драйвер для работы с БД сервера Lucera 2 (Yii 2 / PHP 8.2)
 */
class Lucera2
{
    private Connection $db;
    private string $dbName;

    /**
     * Поля из БД (маппинг)
     * @var array
     */
    protected array $fields = [
        'accounts.access_level' => 'accounts.accessLevel',
        'characters.char_id'    => 'characters.charId',
        'clan_data.clan_id'     => 'clan_data.clan_id',
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

    /**
     * Создание игрового аккаунта
     */
    public function insertAccount(string $login, string $password, int $access_level = 0): bool
    {
        // В Yii 1.1 использовался $this->_context->passwordEncrypt($password)
        // Здесь мы предполагаем, что пароль уже зашифрован или используется сервис.
        // Если нужен специфичный хэш, его лучше передавать уже готовым.
        return $this->db->createCommand()
            ->insert($this->dbName . 'accounts', [
                'login'       => $login,
                'password'    => $password, 
                'accessLevel' => $access_level,
            ])->execute() > 0;
    }

    public function accountsQuery(): Query
    {
        return (new Query())
            ->select([
                'login', 
                'password', 
                'lastactive AS last_active', 
                'accessLevel AS access_level'
            ])
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
                'characters.exp',
                'characters.sp',
                'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFort',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
                'clan_data.clan_name',
                // Специфичный подзапрос для Lucera (проверка тюрьмы)
                '(SELECT IF(valueData>0,1,0) FROM ' . $this->dbName . 'character_data WHERE character_data.charId = characters.charId AND character_data.valueName = "jail" LIMIT 1) as jail',
                '0 as access_level'
            ])
            ->from($this->dbName . 'characters')
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
                'clan_data.hasFort',
                'clan_data.ally_id',
                'clan_data.ally_name',
                'clan_data.leader_id',
                'clan_data.crest_id AS clan_crest',
                'clan_data.crest_large_id AS clan_crest_large',
                'clan_data.ally_crest_id AS ally_crest',
                'clan_data.reputation_score',
                'characters.char_name',
                'characters.account_name',
                'characters.charId AS char_id',
                'characters.level',
                'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.exp', 'characters.sp',
                'characters.karma',
                'characters.pvpkills', 'characters.pkkills',
                'characters.base_class',
                'characters.title',
                'characters.online', 'characters.onlinetime',
                '(SELECT COUNT(0) FROM ' . $this->dbName . 'characters WHERE clanid = clan_data.clan_id) as ccount'
            ])
            ->from($this->dbName . 'clan_data')
            ->leftJoin($this->dbName . 'characters', $this->dbName . 'clan_data.leader_id = ' . $this->dbName . 'characters.charId');
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
     * ВАЖНО: Lucera2 использует таблицу items_external для выдачи!
     */
    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): void
    {
        $this->db->createCommand()
            ->insert($this->dbName . 'items_external', [
                'owner_id'      => $ownerId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'description'   => 'GHTWEB',
                'issued'        => 0,
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
                'issued'        => 0,
            ];
        }

        if (!empty($rows)) {
            $this->db->createCommand()
                ->batchInsert($this->dbName . 'items_external', [
                    'owner_id', 'item_id', 'count', 'enchant_level', 'description', 'issued'
                ], $rows)
                ->execute();
        }
    }

    /* ---------- СТАТИСТИКА (РАСЫ, ОНЛАЙН) ---------- */

    public function getCountRaceHuman(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE race = 0")->queryScalar();
    }

    public function getCountRaceElf(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE race = 1")->queryScalar();
    }

    public function getCountRaceDarkElf(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE race = 2")->queryScalar();
    }

    public function getCountRaceOrk(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE race = 3")->queryScalar();
    }

    public function getCountRaceDwarf(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE race = 4")->queryScalar();
    }

    public function getCountRaceKamael(): int
    {
        // На Lucera2 (Interlude) камаэлей нет
        return 0;
    }

    public function getCountAccounts(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}accounts")->queryScalar();
    }

    public function getCountCharacters(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters")->queryScalar();
    }

    public function getCountOnlineCharacters(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE online = 1")->queryScalar();
    }

    public function getCountClans(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}clan_data")->queryScalar();
    }

    public function getCountMen(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE sex = 0")->queryScalar();
    }

    public function getCountWomen(): int
    {
        return (int)$this->db->createCommand("SELECT COUNT(0) FROM {$this->dbName}characters WHERE sex = 1")->queryScalar();
    }

    /* ---------- ТОПЫ ---------- */

    public function getTopPvp(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->where(['>', 'pvpkills', 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->where(['>', 'pkkills', 0])
            ->orderBy(['pkkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getTop(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
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
            ->where([$this->dbName . 'items.item_id' => 57])
            ->groupBy([$this->dbName . 'characters.charId'])
            ->orderBy(['adena_count' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all($this->db);
    }

    public function getOnline(int $limit = 20, int $offset = 0): array
    {
        return $this->charactersQuery()
            ->where(['online' => 1])
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
                'castle.taxPercent',
                'castle.siegeDate',
                'clan_data.clan_id',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.reputation_score',
                'clan_data.hasCastle',
                'clan_data.ally_id',
                'clan_data.ally_name',
                'clan_data.leader_id',
                'clan_data.crest_id',
                'clan_data.crest_large_id',
                'clan_data.ally_crest_id'
            ])
            ->from($this->dbName . 'castle')
            ->leftJoin($this->dbName . 'clan_data', $this->dbName . 'clan_data.hasCastle = ' . $this->dbName . 'castle.id')
            ->all($this->db);
    }

    public function getSiege(): array
    {
        return (new Query())
            ->select([
                'siege_clans.castle_id',
                'siege_clans.clan_id',
                'siege_clans.type',
                'siege_clans.castle_owner',
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
            ->from($this->dbName . 'account_data')
            ->where(['account_name' => $accountName, 'valueName' => 'premium'])
            ->limit(1)
            ->one($this->db);

        return [
            'dateEnd' => ($res && $res['valueData'] > 0 ? substr($res['valueData'], 0, 10) : 0),
        ];
    }

    public function addPremium(string $accountName, int $timeEnd, bool $isIssetPremium = false): bool
    {
        $timeEnd .= '000';

        if ($isIssetPremium) {
            return $this->db->createCommand()
                ->update(
                    $this->dbName . 'account_data',
                    ['valueData' => $timeEnd],
                    ['valueName' => 'premium', 'account_name' => $accountName]
                )->execute() > 0;
        }

        return $this->db->createCommand()
            ->insert($this->dbName . 'account_data', [
                'account_name' => $accountName,
                'valueName'    => 'premium',
                'valueData'    => $timeEnd,
            ])->execute() > 0;
    }

    public function removeHWID(string $accountName): bool
    {
        return $this->db->createCommand()
            ->update(
                $this->dbName . 'accounts',
                ['allowed_hwid' => '*'],
                ['login' => $accountName]
            )->execute() > 0;
    }

    /* ---------- КОНТРОЛЬ ПРЕДМЕТОВ ---------- */

    public function getItemsControl(array $itemsIds): array
    {
        if (!$itemsIds) {
            return [];
        }

        // В Yii 2 получаем модели через ActiveRecord
        $resAllItems = AllItems::find()->where(['item_id' => $itemsIds])->all();
        $itemNames = [];
        foreach ($resAllItems as $row) {
            // В Yii 2 getPrimaryKey() возвращает массив, поэтому используем свойство item_id
            $itemNames[$row->item_id] = $row;
        }

        $res = (new Query())
            ->select([
                'MAX(items.count) AS maxCountItems',
                'COUNT(items.count) AS countItems',
                'items.owner_id', 'items.object_id', 'items.item_id',
                'items.count', 'items.enchant_level', 'items.loc', 'items.loc_data',
                'characters.charId AS char_id',
                'characters.account_name', 'characters.char_name',
                'characters.level', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'characters.maxHp', 'characters.x', 'characters.y', 'characters.z',
                'characters.exp', 'characters.sp', 'characters.pvpkills', 'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.base_class', 'characters.title',
                'characters.online', 'characters.onlinetime',
                'clan_data.clan_name', 'clan_data.clan_level',
                'clan_data.hasCastle', 'clan_data.hasFort',
                'clan_data.crest_id AS clan_crest', 'clan_data.reputation_score'
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
        return 'interlude';
    }

    public function getServerName(): string
    {
        return static::class;
    }
}