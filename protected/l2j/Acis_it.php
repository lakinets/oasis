<?php
declare(strict_types=1);

namespace app\l2j;

use Yii;
use yii\db\Command;
use app\models\AllItems;

/**
 * Адаптер для aCis Interlude (Yii 2.2 / PHP 8.2)
 */
final class Acis_it extends ServerAdapterBase
{
    public function getChronicle(): string
    {
        return 'it';
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
            ->select(['login', 'password', 'access_level', 'lastactive AS last_active'])
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
                'characters.race AS base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp',
                'characters.curHp',
                'characters.maxCp',
                'characters.curCp',
                'characters.maxMp',
                'characters.curMp',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
            ])
            ->from('characters')
            ->leftJoin('clan_data', 'clan_data.clan_id = characters.clanid');
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
                'clan_data.leader_id',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
                '(SELECT COUNT(*) FROM characters WHERE characters.clanid = clan_data.clan_id) AS ccount',
                'ally_name',
                'ally_crest_id AS ally_crest',
                'ally_id',
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
                'characters.race AS base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp',
                'characters.curHp',
                'characters.maxCp',
                'characters.curCp',
                'characters.maxMp',
                'characters.curMp',
            ])
            ->from('clan_data')
            ->leftJoin('characters', 'characters.obj_Id = clan_data.leader_id');
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
        $maxId = (int)$this->_db->createCommand('SELECT MAX(object_id) + 1 FROM items')->queryScalar();
        return $this->_db->createCommand(
            'INSERT INTO items (owner_id, object_id, item_id, count, enchant_level, loc)
             VALUES (:owner_id, :object_id, :item_id, :count, :enchant_level, :loc)',
            [
                ':owner_id'      => $ownerId,
                ':object_id'     => $maxId ?: 1,
                ':item_id'       => $itemId,
                ':count'         => $count,
                ':enchant_level' => $enchantLevel,
                ':loc'           => 'INVENTORY',
            ]
        )->execute();
    }

    public function multiInsertItem(array $items): int
    {
        $maxId = (int)$this->_db->createCommand('SELECT MAX(object_id) + 1 FROM items')->queryScalar();
        foreach ($items as &$row) {
            $row['object_id']   = $maxId++;
            $row['enchant_level'] = $row['enchant'] ?? 0;
            $row['loc']         = 'INVENTORY';
            $row['loc_data']    = 0;
            unset($row['enchant']);
        }
        return $this->_db->createCommand()
            ->batchInsert('items', ['owner_id', 'object_id', 'item_id', 'count', 'enchant_level', 'loc', 'loc_data'], $items)
            ->execute();
    }

    /* -----------------------------------------------------------------
     *  Расы
     * ----------------------------------------------------------------- */
    public function getCountRaceHuman(): int   { return $this->getRaceCount(0); }
    public function getCountRaceElf(): int     { return $this->getRaceCount(1); }
    public function getCountRaceDarkElf(): int { return $this->getRaceCount(2); }
    public function getCountRaceOrk(): int     { return $this->getRaceCount(3); }
    public function getCountRaceDwarf(): int   { return $this->getRaceCount(4); }
    public function getCountRaceKamael(): int  { return $this->getRaceCount(5); }

    private function getRaceCount(int $raceId): int
    {
        return (int)$this->_db->createCommand(
            'SELECT COUNT(*) FROM characters WHERE race = :race',
            [':race' => $raceId]
        )->queryScalar();
    }

    /* -----------------------------------------------------------------
     *  Статистика
     * ----------------------------------------------------------------- */
    public function getCountAccounts(): int         { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM accounts')->queryScalar(); }
    public function getCountCharacters(): int       { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters')->queryScalar(); }
    public function getCountOnlineCharacters(): int { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE online = 1')->queryScalar(); }
    public function getCountClans(): int            { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM clan_data')->queryScalar(); }
    public function getCountMen(): int              { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE sex = 0')->queryScalar(); }
    public function getCountWomen(): int            { return (int)$this->_db->createCommand('SELECT COUNT(*) FROM characters WHERE sex = 1')->queryScalar(); }
    public function getCountOfflineTraders(): int   { return 0; /* TODO */ }

    /* -----------------------------------------------------------------
     *  Топы
     * ----------------------------------------------------------------- */
    public function getTopPvp(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where('pvpkills > 0')
            ->andWhere('accesslevel = 0')
            ->orderBy('pvpkills DESC')
            ->limit($limit)
            ->offset($offset)
            ->queryAll();
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where('pkkills > 0')
            ->andWhere('accesslevel = 0')
            ->orderBy('pkkills DESC')
            ->limit($limit)
            ->offset($offset)
            ->queryAll();
    }

    public function getTop(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where('accesslevel = 0')
            ->orderBy('exp DESC, sp DESC')
            ->limit($limit)
            ->offset($offset)
            ->queryAll();
    }

    public function getTopRich(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->select([
                '{{characters}}.*',
                'SUM(items.count) AS adena_count',
            ])
            ->leftJoin('items', 'items.owner_id = {{characters}}.obj_Id')
            ->where('items.item_id = 57')
            ->andWhere('{{characters}}.accesslevel = 0')
            ->groupBy('{{characters}}.obj_Id')
            ->orderBy('adena_count DESC')
            ->limit($limit)
            ->offset($offset)
            ->queryAll();
    }

    public function getOnline(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where('online = 1')
            ->andWhere('accesslevel = 0')
            ->orderBy('level DESC')
            ->limit($limit)
            ->offset($offset)
            ->queryAll();
    }

    public function getTopClans(int $limit = 20, int $offset = 0): array
    {
        return $this->clans()
            ->orderBy('reputation_score DESC')
            ->limit($limit)
            ->offset($offset)
            ->queryAll();
    }

    /* -----------------------------------------------------------------
     *  Замки и осады
     * ----------------------------------------------------------------- */
    public function getCastles(): array
    {
        return $this->_db->createCommand(
            'SELECT castle.id, castle.name, castle.taxPercent, castle.siegeDate,
                    clan_data.clan_id, clan_data.clan_name, clan_data.leader_id,
                    clan_data.clan_level, clan_data.hasCastle,
                    clan_data.crest_id AS clan_crest, clan_data.reputation_score,
                    ally_name, ally_crest_id AS ally_crest, ally_id
             FROM castle
             LEFT JOIN clan_data ON castle.id = clan_data.hasCastle'
        )->queryAll();
    }

    public function getSiege(): array
    {
        return $this->_db->createCommand(
            'SELECT siege_clans.castle_id, siege_clans.type,
                    clan_data.clan_id, clan_data.clan_name,
                    clan_data.leader_id, clan_data.clan_level,
                    clan_data.hasCastle, clan_data.crest_id AS clan_crest,
                    clan_data.reputation_score, ally_name,
                    ally_crest_id AS ally_crest, ally_id
             FROM siege_clans
             LEFT JOIN clan_data ON siege_clans.clan_id = clan_data.clan_id'
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
                 items.owner_id, items.object_id, items.item_id, items.count,
                 items.enchant_level, items.loc, items.loc_data,
                 characters.account_name, characters.obj_Id AS char_id,
                 characters.char_name, characters.sex, characters.x,
                 characters.y, characters.z, characters.karma,
                 characters.pvpkills, characters.pkkills,
                 characters.clanid AS clan_id, characters.title,
                 characters.accesslevel AS access_level, characters.online,
                 characters.onlinetime, characters.race AS base_class,
                 characters.level, characters.exp, characters.sp,
                 characters.maxHp, characters.curHp, characters.maxCp,
                 characters.curCp, characters.maxMp, characters.curMp,
                 clan_data.clan_name, clan_data.clan_level,
                 clan_data.hasCastle, clan_data.crest_id AS clan_crest,
                 clan_data.reputation_score
             FROM items
             LEFT JOIN characters ON items.owner_id = characters.obj_Id
             LEFT JOIN clan_data ON clan_data.clan_id = characters.clanid
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