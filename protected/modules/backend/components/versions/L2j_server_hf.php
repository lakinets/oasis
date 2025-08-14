<?php
declare(strict_types=1);

namespace app\l2j;

use Yii;
use yii\db\Command;
use app\models\AllItems;

/**
 * Адаптер для L2j-сервера High Five (Yii 2.2 / PHP 8.2)
 */
final class L2j_server_hf extends ServerAdapterBase
{
    /* -----------------------------------------------------------------
     *  Базовые константы
     * ----------------------------------------------------------------- */
    public function getChronicle(): string
    {
        return 'hf';
    }

    public function getServerName(): string
    {
        return self::class;
    }

    /* -----------------------------------------------------------------
     *  Поля БД
     * ----------------------------------------------------------------- */
    protected array $_fields = [
        'accounts.access_level'   => 'accounts.accessLevel',
        'characters.access_level' => 'characters.accesslevel',
        'characters.char_id'      => 'characters.charId',
        'clan_data.clan_id'       => 'clan_data.clan_id',
    ];

    /* -----------------------------------------------------------------
     *  Аккаунты
     * ----------------------------------------------------------------- */
    public function insertAccount(string $login, string $password, int $accessLevel = 0): int
    {
        $this->_db->createCommand()
            ->insert('accounts', [
                'login'       => $login,
                'password'    => $this->_context->passwordEncrypt($password),
                'accessLevel' => $accessLevel,
            ])
            ->execute();

        $this->_db->createCommand()
            ->insert('account_premium', [
                'account_name'    => $login,
                'premium_service' => 0,
                'enddate'         => 0,
            ])
            ->execute();

        return 1;
    }

    public function accounts(?Command $command = null): Command
    {
        $command = $command ?? $this->_db->createCommand();
        return $command
            ->select(['login', 'password', 'lastactive AS last_active', 'accessLevel AS access_level'])
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
                'characters.charId AS char_id',
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
                'characters.online',
                'characters.onlinetime',
                'characters.base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp',
                'characters.maxMp',
                'characters.maxCp',
                'characters.curHp',
                'characters.curMp',
                'characters.curCp',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
                'clan_data.clan_name',
                'characters.accesslevel AS access_level',
                '(SELECT IF(val>0,1,0) FROM character_variables
                  WHERE character_variables.charId = characters.charId
                    AND character_variables.var = "jailed" LIMIT 1) AS jail',
                'fort.id AS hasFort',
            ])
            ->from('characters')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->leftJoin('fort', 'characters.charId = fort.owner');
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
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
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
                'characters.curHp',
                'characters.curMp',
                'characters.curCp',
                'characters.maxHp',
                'characters.maxMp',
                'characters.maxCp',
                'characters.sex',
                'characters.x',
                'characters.y',
                'characters.z',
                'characters.exp',
                'characters.sp',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.classid AS base_class',
                'characters.title',
                'characters.online',
                'characters.onlinetime',
                'characters.accesslevel AS access_level',
                '(SELECT COUNT(*) FROM characters WHERE clanid = clan_data.clan_id) AS ccount',
                'fort.id AS hasFort',
            ])
            ->from('clan_data')
            ->leftJoin('characters', 'clan_data.leader_id = characters.charId')
            ->leftJoin('fort', 'characters.charId = fort.owner');
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
        $maxId = (int)$this->_db->createCommand('SELECT MAX(object_id)+1 FROM items')->queryScalar();
        return $this->_db->createCommand()
            ->insert('items', [
                'owner_id'      => $ownerId,
                'object_id'     => $maxId ?: 1,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'log'           => 'INVENTORY',
            ])
            ->execute();
    }

    public function multiInsertItem(array $items): int
    {
        $inserted = 0;
        foreach ($items as $row) {
            $inserted += $this->insertItem(
                $row['owner_id'],
                $row['item_id'],
                $row['count'] ?? 1,
                $row['enchant'] ?? 0
            );
        }
        return $inserted;
    }

    /* -----------------------------------------------------------------
     *  Расы
     * ----------------------------------------------------------------- */
    public function getCountRaceHuman(): int   { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 0')->queryScalar(); }
    public function getCountRaceElf(): int     { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 1')->queryScalar(); }
    public function getCountRaceDarkElf(): int { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 2')->queryScalar(); }
    public function getCountRaceOrk(): int     { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 3')->queryScalar(); }
    public function getCountRaceDwarf(): int   { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 4')->queryScalar(); }
    public function getCountRaceKamael(): int  { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 5')->queryScalar(); }

    /* -----------------------------------------------------------------
     *  Премиум / HWID
     * ----------------------------------------------------------------- */
    public function getPremiumInfo(string $accountName): array
    {
        $row = $this->_db->createCommand(
            'SELECT enddate FROM account_premium WHERE account_name = :account_name AND premium_service = 1 LIMIT 1',
            [':account_name' => $accountName]
        )->queryOne();

        return ['dateEnd' => $row ? (int)substr((string)$row['enddate'], 0, 10) : 0];
    }

    public function addPremium(string $accountName, int $timeEnd): int
    {
        return $this->_db->createCommand(
            'UPDATE account_premium SET enddate = :enddate, premium_service = 1 WHERE account_name = :account_name',
            [':enddate' => $timeEnd, ':account_name' => $accountName]
        )->execute();
    }

    public function removeHWID(string $accountName): bool
    {
        // реализуйте по фактической таблице HWID
        return true;
    }

    /* -----------------------------------------------------------------
     *  Замки и осады
     * ----------------------------------------------------------------- */
    public function getCastles(): array
    {
        return $this->_db->createCommand(
            'SELECT castle.id, castle.name, castle.taxPercent, castle.siegeDate,
                    clan_data.clan_id, clan_data.clan_level, clan_data.reputation_score,
                    clan_data.hasCastle, clan_data.clan_name, clan_data.ally_id,
                    clan_data.ally_name, clan_data.leader_id,
                    clan_data.crest_id AS clan_crest,
                    clan_data.crest_large_id AS clan_crest_large,
                    clan_data.ally_crest_id AS ally_crest
             FROM castle
             LEFT JOIN clan_data ON clan_data.hasCastle = castle.id
             LEFT JOIN characters ON clan_data.leader_id = characters.charId'
        )->queryAll();
    }

    public function getSiege(): array
    {
        return $this->_db->createCommand(
            'SELECT siege_clans.castle_id, siege_clans.clan_id, siege_clans.type,
                    clan_data.clan_name, clan_data.clan_level, clan_data.reputation_score,
                    clan_data.hasCastle, clan_data.ally_id, clan_data.ally_name,
                    clan_data.leader_id, clan_data.crest_id AS clan_crest,
                    clan_data.crest_large_id AS clan_crest_large,
                    clan_data.ally_crest_id AS ally_crest
             FROM siege_clans
             LEFT JOIN clan_data ON siege_clans.clan_id = clan_data.clan_id'
        )->queryAll();
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
                 items.owner_id, items.object_id, items.item_id, items.count,
                 items.enchant_level, items.loc, items.loc_data,
                 characters.charId AS char_id, characters.account_name,
                 characters.char_name, characters.sex, characters.x, characters.y,
                 characters.z, characters.karma, characters.pvpkills,
                 characters.pkkills, characters.clanid AS clan_id,
                 characters.title, characters.accesslevel AS access_level,
                 characters.online, characters.onlinetime,
                 characters.classid AS base_class, characters.level,
                 characters.exp, characters.sp, characters.curHp,
                 characters.curMp, characters.curCp, characters.maxHp,
                 characters.maxMp, characters.maxCp, clan_data.clan_name,
                 clan_data.clan_level, clan_data.hasCastle,
                 clan_data.crest_id AS clan_crest, clan_data.reputation_score
             FROM items
             LEFT JOIN characters ON items.owner_id = characters.charId
             LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
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