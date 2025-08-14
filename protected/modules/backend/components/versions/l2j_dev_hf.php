<?php
declare(strict_types=1);

namespace app\l2j;

use Yii;
use yii\db\Command;
use app\models\AllItems;

/**
 * Адаптер для L2j-Dev High-Five (Yii 2.2 / PHP 8.2)
 */
final class l2j_dev_hf extends ServerAdapterBase
{
    public function getChronicle(): string
    {
        return 'hf';
    }

    public function getServerName(): string
    {
        return self::class;
    }

    protected array $_fields = [
        'accounts.access_level'   => 'accounts.access_level',
        'characters.access_level' => 'characters.accesslevel',
        'characters.char_id'      => 'characters.obj_Id',
        'clan_data.clan_id'       => 'clan_data.clan_id',
    ];

    /* -----------------------------------------------------------------
     *  Аккаунты
     * ----------------------------------------------------------------- */
    public function insertAccount(string $login, string $password, int $accessLevel = 0): int
    {
        return $this->_db->createCommand(
            'INSERT INTO accounts (login, password, access_level)
             VALUES (:login, :password, :access_level)',
            [
                ':login'        => $login,
                ':password'     => $this->_context->passwordEncrypt($password),
                ':access_level' => $accessLevel,
            ]
        )->execute();
    }

    public function accounts(?Command $command = null): Command
    {
        $command = $command ?? $this->_db->createCommand();
        return $command
            ->select(['login', 'password', 'access_level', 'last_access AS last_active'])
            ->from('accounts');
    }

    /* -----------------------------------------------------------------
     *  Персонажи
     * ----------------------------------------------------------------- */
    public function characters(?Command $command = null): Command
    {
        $command = $command ?? $this->_db->createCommand();
        return $command
            ->select([
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x',
                'characters.y',
                'characters.z',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.title',
                'characters.accesslevel AS access_level',
                'characters.online',
                'characters.onlinetime',
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'character_subclasses.maxHp',
                'character_subclasses.curHp',
                'character_subclasses.maxCp',
                'character_subclasses.curCp',
                'character_subclasses.maxMp',
                'character_subclasses.curMp',
                'clan_subpledges.name AS clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
            ])
            ->from('characters')
            ->leftJoin('character_subclasses', 'character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase = 1')
            ->leftJoin('clan_data', 'clan_data.clan_id = characters.clanid')
            ->leftJoin('clan_subpledges', 'clan_subpledges.clan_id = clan_data.clan_id AND clan_subpledges.type = 0');
    }

    /* -----------------------------------------------------------------
     *  Кланы
     * ----------------------------------------------------------------- */
    public function clans(?Command $command = null): Command
    {
        $command = $command ?? $this->_db->createCommand();
        return $command
            ->select([
                'clan_data.clan_id',
                'clan_subpledges.name AS clan_name',
                'clan_subpledges.leader_id',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
                '(SELECT COUNT(*) FROM characters WHERE characters.clanid = clan_data.clan_id) AS ccount',
                'ally_data.ally_name',
                'ally_data.crest AS ally_crest',
                'ally_data.ally_id',
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x',
                'characters.y',
                'characters.z',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.title',
                'characters.accesslevel AS access_level',
                'characters.online',
                'characters.onlinetime',
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'character_subclasses.maxHp',
                'character_subclasses.curHp',
                'character_subclasses.maxCp',
                'character_subclasses.curCp',
                'character_subclasses.maxMp',
                'character_subclasses.curMp',
            ])
            ->from('clan_data')
            ->leftJoin('clan_subpledges', 'clan_subpledges.clan_id = clan_data.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->leftJoin('characters', 'characters.obj_Id = clan_subpledges.leader_id')
            ->leftJoin('character_subclasses', 'character_subclasses.char_obj_id = characters.obj_Id AND character_subclasses.isBase = 1');
    }

    /* -----------------------------------------------------------------
     *  Предметы
     * ----------------------------------------------------------------- */
    public function items(?Command $command = null): Command
    {
        $command = $command ?? $this->_db->createCommand();
        return $command
            ->select(['owner_id', 'object_id', 'item_id', 'count', 'enchant_level', 'loc', 'loc_data'])
            ->from('items');
    }

    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): int
    {
        return $this->_db->createCommand(
            'INSERT INTO items_delayed (owner_id, item_id, count, enchant_level, description)
             VALUES (:owner_id, :item_id, :count, :enchant_level, :description)',
            [
                ':owner_id'      => $ownerId,
                ':item_id'       => $itemId,
                ':count'         => $count,
                ':enchant_level' => $enchantLevel,
                ':description'   => 'GHTWEB v5',
            ]
        )->execute();
    }

    public function multiInsertItem(array $items): int
    {
        foreach ($items as &$row) {
            $row['description']   = 'GHTWEB v5';
            $row['enchant_level'] = $row['enchant'] ?? 0;
            unset($row['enchant']);
        }
        return $this->_db->createCommand()
            ->batchInsert('items_delayed', ['owner_id', 'item_id', 'count', 'enchant_level', 'description'], $items)
            ->execute();
    }

    /* -----------------------------------------------------------------
     *  Расы
     * ----------------------------------------------------------------- */
    public function getCountRaceHuman(): int   { return $this->getRaceCountByClassIds([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,88,89,90,91,92,93,94,95,96,97,98]); }
    public function getCountRaceElf(): int     { return $this->getRaceCountByClassIds([18,19,20,21,22,23,24,25,26,27,28,29,30,99,100,101,102,103,104,105]); }
    public function getCountRaceDarkElf(): int { return $this->getRaceCountByClassIds([31,32,33,34,35,36,37,38,39,40,41,42,43,106,107,108,109,110,111,112]); }
    public function getCountRaceOrk(): int     { return $this->getRaceCountByClassIds([44,45,46,47,48,49,50,51,52,113,114,115,116]); }
    public function getCountRaceDwarf(): int   { return $this->getRaceCountByClassIds([53,54,55,56,57,117,118]); }
    public function getCountRaceKamael(): int  { return $this->getRaceCountByClassIds([123,124,125,126,127,128,129,130,131,132,133,134,135,136,139,140,141,142,143,144,145,146]); }

    private function getRaceCountByClassIds(array $ids): int
    {
        return (int)$this->_db->createCommand(
            'SELECT COUNT(*) FROM character_subclasses
             WHERE class_id IN (' . implode(',', $ids) . ') AND isBase = 1'
        )->queryScalar();
    }

    /* -----------------------------------------------------------------
     *  Замки и осады
     * ----------------------------------------------------------------- */
    public function getCastles(): array
    {
        return $this->_db->createCommand(
            'SELECT castle.id, castle.name, castle.tax_percent AS taxPercent,
                    castle.siege_date AS siegeDate, clan_data.clan_id,
                    clan_subpledges.name AS clan_name,
                    clan_subpledges.leader_id, clan_data.clan_level,
                    clan_data.hasCastle, clan_data.crest AS clan_crest,
                    clan_data.reputation_score, ally_data.ally_name,
                    ally_data.crest AS ally_crest, ally_data.ally_id
             FROM castle
             LEFT JOIN clan_data ON castle.id = clan_data.hasCastle
             LEFT JOIN clan_subpledges ON clan_subpledges.clan_id = clan_data.clan_id
                                       AND clan_subpledges.type = 0
             LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id'
        )->queryAll();
    }

    public function getSiege(): array
    {
        return $this->_db->createCommand(
            'SELECT siege_clans.residence_id AS castle_id,
                    IF(siege_clans.type="attackers",1,0) AS type,
                    clan_data.clan_id,
                    clan_subpledges.name AS clan_name,
                    clan_subpledges.leader_id, clan_data.clan_level,
                    clan_data.hasCastle, clan_data.crest AS clan_crest,
                    clan_data.reputation_score, ally_data.ally_name,
                    ally_data.crest AS ally_crest, ally_data.ally_id
             FROM siege_clans
             LEFT JOIN clan_data ON siege_clans.clan_id = clan_data.clan_id
             LEFT JOIN clan_subpledges ON clan_subpledges.clan_id = clan_data.clan_id
                                       AND clan_subpledges.type = 0
             LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id'
        )->queryAll();
    }

    /* -----------------------------------------------------------------
     *  Премиум / HWID
     * ----------------------------------------------------------------- */
    public function getPremiumInfo(string $accountName): array
    {
        // TODO реализовать под конкретную схему
        return ['dateEnd' => 0];
    }

    public function addPremium(string $accountName, int $timeEnd): int
    {
        // TODO реализовать
        return 0;
    }

    public function removeHWID(string $accountName): bool
    {
        // TODO реализовать
        return false;
    }

    /* -----------------------------------------------------------------
     *  Контроль предметов
     * ----------------------------------------------------------------- */
    public function getItemsControl(array $itemsIds): array
    {
        if (!$itemsIds) {
            return [];
        }

        $items = AllItems::findAll(['item_id' => $itemsIds]);
        $names = [];
        foreach ($items as $item) {
            $names[$item->item_id] = $item;
        }

        $rows = $this->_db->createCommand(
            'SELECT
                 MAX(items.count) AS maxCountItems,
                 COUNT(items.count) AS countItems,
                 items.owner_id, items.object_id, items.item_id,
                 items.count, items.enchant_level, items.loc, items.loc_data,
                 characters.account_name, characters.obj_Id AS char_id,
                 characters.char_name, characters.sex, characters.x,
                 characters.y, characters.z, characters.karma,
                 characters.pvpkills, characters.pkkills,
                 characters.clanid AS clan_id, characters.title,
                 characters.accesslevel AS access_level, characters.online,
                 characters.onlinetime, character_subclasses.class_id AS base_class,
                 character_subclasses.level, character_subclasses.exp,
                 character_subclasses.sp, character_subclasses.maxHp,
                 character_subclasses.curHp, character_subclasses.maxCp,
                 character_subclasses.curCp, character_subclasses.maxMp,
                 character_subclasses.curMp, clan_subpledges.name AS clan_name,
                 clan_data.clan_level, clan_data.hasCastle,
                 clan_data.crest AS clan_crest, clan_data.reputation_score
             FROM items
             LEFT JOIN characters ON items.owner_id = characters.obj_Id
             LEFT JOIN character_subclasses ON character_subclasses.char_obj_id = characters.obj_Id
                                       AND character_subclasses.isBase = 1
             LEFT JOIN clan_data ON clan_data.clan_id = characters.clanid
             LEFT JOIN clan_subpledges ON clan_subpledges.clan_id = clan_data.clan_id
                                       AND clan_subpledges.type = 0
             WHERE items.item_id IN (' . implode(',', $itemsIds) . ')
             GROUP BY items.owner_id, items.item_id
             ORDER BY maxCountItems DESC'
        )->queryAll();

        $result = [];
        foreach ($rows as $row) {
            $id = $row['item_id'];
            if (!isset($result[$id])) {
                $result[$id] = [
                    'itemInfo'      => $names[$id] ?? null,
                    'characters'    => [],
                    'maxTotalItems' => 0,
                    'totalItems'    => 0,
                ];
            }
            $result[$id]['characters'][]   = $row;
            $result[$id]['maxTotalItems'] += $row['maxCountItems'];
            $result[$id]['totalItems']     = count($result[$id]['characters']);
        }

        foreach (array_diff_key($names, $result) as $item) {
            $result[$item->item_id] = [
                'itemInfo'      => $item,
                'characters'    => [],
                'maxTotalItems' => 0,
                'totalItems'    => 0,
            ];
        }
        return $result;
    }
}