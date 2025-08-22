<?php
declare(strict_types=1);

namespace app\l2j;

use Yii;
use yii\db\Command;
use app\models\AllItems;

/**
 * Адаптер для L2-dev High-Five (Yii 2.2 / PHP 8.2)
 */
final class L2_dev_hf extends ServerAdapterBase
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
        return $this->_db->createCommand()
            ->insert('accounts', [
                'login'        => $login,
                'password'     => $this->_context->passwordEncrypt($password),
                'access_level' => $accessLevel,
            ])
            ->execute();
    }

    public function accounts(?Command $command = null): Command
    {
        $command = $command ?? $this->_db->createCommand();
        return $command
            ->select(['login', 'password', 'last_access AS last_active', 'access_level'])
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
                'character_subclasses.curHp',
                'character_subclasses.curMp',
                'character_subclasses.curCp',
                'character_subclasses.maxHp',
                'character_subclasses.maxMp',
                'character_subclasses.maxCp',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFortress AS hasFort',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
                'clan_subpledges.name AS clan_name',
                '(SELECT IF(value>0,1,0) FROM character_variables WHERE character_variables.obj_id = characters.obj_id AND character_variables.name = "jailed" LIMIT 1) AS jail',
            ])
            ->from('characters')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id');
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
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFortress',
                'clan_data.ally_id',
                'ally_data.ally_name',
                'clan_subpledges.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'ally_data.crest AS ally_crest',
                'clan_data.reputation_score',
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'character_subclasses.level',
                'character_subclasses.maxHp',
                'character_subclasses.curHp',
                'character_subclasses.maxCp',
                'character_subclasses.curCp',
                'character_subclasses.maxMp',
                'character_subclasses.curMp',
                'characters.sex',
                'characters.x',
                'characters.y',
                'characters.z',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'character_subclasses.class_id AS base_class',
                'characters.title',
                'characters.online',
                'characters.onlinetime',
                '(SELECT COUNT(*) FROM characters WHERE characters.clanid = clan_data.clan_id) AS ccount',
            ])
            ->from('clan_data')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('characters', 'clan_subpledges.leader_id = characters.obj_Id')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_Id AND character_subclasses.isBase = 1')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id');
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
        return $this->_db->createCommand()
            ->insert('items_delayed', [
                'owner_id'      => $ownerId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'description'   => 'GHTWEB',
            ])
            ->execute();
    }

    public function multiInsertItem(array $items): int
    {
        foreach ($items as &$row) {
            $row['description']   = 'GHTWEB';
            $row['enchant_level'] = $row['enchant'] ?? 0;
            unset($row['enchant']);
        }
        return $this->_db->createCommand()
            ->batchInsert('items_delayed', ['owner_id', 'item_id', 'count', 'enchant_level', 'description'], $items)
            ->execute();
    }

    /* -----------------------------------------------------------------
     *  Расы (через params)
     * ----------------------------------------------------------------- */
    public function getCountRaceHuman(): int   { return $this->getRaceCountById(0); }
    public function getCountRaceElf(): int     { return $this->getRaceCountById(1); }
    public function getCountRaceDarkElf(): int { return $this->getRaceCountById(2); }
    public function getCountRaceOrk(): int     { return $this->getRaceCountById(3); }
    public function getCountRaceDwarf(): int   { return $this->getRaceCountById(4); }
    public function getCountRaceKamael(): int  { return $this->getRaceCountById(5); }

    private function getRaceCountById(int $id): int
    {
        $list = Yii::$app->params['l2']['classList'] ?? [];
        $ids  = [];
        foreach ($list as $k => $v) {
            if ((int)($v['race'] ?? -1) === $id) {
                $ids[] = (int)$k;
            }
        }
        if (!$ids) {
            return 0;
        }
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
                    clan_data.clan_level, clan_data.reputation_score,
                    clan_data.hasCastle, clan_data.hasFortress AS hasFort,
                    clan_subpledges.name AS clan_name, clan_data.ally_id,
                    ally_data.ally_name, clan_subpledges.leader_id,
                    clan_data.crest AS clan_crest,
                    clan_data.largecrest AS clan_crest_large,
                    ally_data.crest AS ally_crest
             FROM castle
             LEFT JOIN clan_data ON clan_data.hasCastle = castle.id
             LEFT JOIN clan_subpledges ON clan_data.clan_id = clan_subpledges.clan_id
                                       AND clan_subpledges.type = 0
             LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id'
        )->queryAll();
    }

    public function getSiege(): array
    {
        return $this->_db->createCommand(
            'SELECT siege_clans.residence_id AS castle_id,
                    siege_clans.clan_id,
                    siege_clans.type,
                    clan_subpledges.name AS clan_name,
                    clan_data.clan_level,
                    clan_data.reputation_score,
                    clan_data.hasCastle,
                    clan_data.hasFortress AS hasFort,
                    clan_data.ally_id,
                    ally_data.ally_name,
                    clan_subpledges.leader_id,
                    clan_data.crest AS clan_crest,
                    clan_data.largecrest AS clan_crest_large,
                    ally_data.crest AS ally_crest
             FROM siege_clans
             LEFT JOIN clan_data ON siege_clans.clan_id = clan_data.clan_id
             LEFT JOIN clan_subpledges ON clan_data.clan_id = clan_subpledges.clan_id
                                       AND clan_subpledges.type = 0
             LEFT JOIN ally_data ON clan_data.ally_id = ally_data.ally_id
             WHERE clan_subpledges.type = 0'
        )->queryAll();
    }

    /* -----------------------------------------------------------------
     *  Премиум / HWID
     * ----------------------------------------------------------------- */
    public function getPremiumInfo(string $accountName): array
    {
        $row = $this->_db->createCommand(
            'SELECT bonus_expire FROM accounts WHERE login = :login AND bonus_expire > 0 LIMIT 1',
            [':login' => $accountName]
        )->queryOne();

        return ['dateEnd' => $row ? (int)substr((string)$row['bonus_expire'], 0, 10) : 0];
    }

    public function addPremium(string $accountName, int $timeEnd): int
    {
        return $this->_db->createCommand()
            ->update('accounts', ['bonus_expire' => $timeEnd], 'login = :login', [':login' => $accountName])
            ->execute();
    }

    public function removeHWID(string $accountName): bool
    {
        return (bool)$this->_db->createCommand()
            ->update('accounts', ['allow_hwid' => ''], 'login = :login', [':login' => $accountName])
            ->execute();
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
                 Max(items.count) AS maxCountItems,
                 Count(items.count) AS countItems,
                 items.owner_id,
                 items.object_id,
                 items.item_id,
                 items.count,
                 items.enchant_level,
                 items.loc,
                 items.loc_data,
                 characters.obj_Id AS char_id,
                 characters.account_name,
                 characters.char_name,
                 characters.x,
                 characters.y,
                 characters.z,
                 characters.karma,
                 characters.pvpkills,
                 characters.pkkills,
                 characters.clanid AS clan_id,
                 characters.title,
                 characters.online,
                 characters.onlinetime,
                 clan_data.clan_level,
                 clan_data.hasFortress AS hasFort,
                 clan_data.hasCastle,
                 clan_data.crest AS clan_crest,
                 clan_data.reputation_score,
                 character_subclasses.class_id AS base_class,
                 character_subclasses.level,
                 character_subclasses.exp,
                 character_subclasses.sp,
                 character_subclasses.curHp,
                 character_subclasses.curMp,
                 character_subclasses.curCp,
                 character_subclasses.maxHp,
                 character_subclasses.maxMp,
                 character_subclasses.maxCp,
                 clan_subpledges.name AS clan_name
             FROM items
             LEFT JOIN characters ON items.owner_id = characters.obj_Id
             LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
             LEFT JOIN character_subclasses ON characters.obj_Id = character_subclasses.char_obj_id
                                       AND character_subclasses.isBase = 1
             LEFT JOIN clan_subpledges ON clan_data.clan_id = clan_subpledges.clan_id
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