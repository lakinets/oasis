<?php
namespace app\modules\backend\components\versions;

use yii\db\Connection;
use yii\db\Query;

class L2jLovelyIt
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    // -----------------------------
    // АККАУНТЫ
    // -----------------------------
    public function insertAccount(string $login, string $password, int $accessLevel = 0): bool
    {
        return $this->db->createCommand()
            ->insert('accounts', [
                'login'        => $login,
                'password'     => $password, // уже зашифровано
                'accessLevel'  => $accessLevel,
            ])
            ->execute() > 0;
    }

    public function accounts(): Query
    {
        return (new Query())
            ->select(['login', 'password', 'accessLevel AS access_level', 'lastactive AS last_active'])
            ->from('accounts');
    }

    // -----------------------------
    // ПЕРСОНАЖИ
    // -----------------------------
    public function characters(): Query
    {
        return (new Query())
            ->select([
                'characters.account_name',
                'characters.charId AS char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.title',
                '0 AS access_level',
                'characters.online',
                'characters.onlinetime',
                'characters.race AS base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
            ])
            ->from('characters')
            ->leftJoin('clan_data', 'clan_data.clan_id = characters.clanid');
    }

    // -----------------------------
    // КЛАНЫ
    // -----------------------------
    public function clans(): Query
    {
        return (new Query())
            ->select([
                'clan_data.clan_id', 'clan_data.clan_name', 'clan_data.leader_id',
                'clan_data.clan_level', 'clan_data.hasCastle', 'clan_data.crest_id AS clan_crest',
                'clan_data.reputation_score',
                'clan_data.ally_id', 'clan_data.ally_name', 'clan_data.ally_crest_id AS ally_crest',
                'characters.char_name', 'characters.account_name', 'characters.charId AS char_id',
                'characters.level', 'characters.maxHp', 'characters.curHp', 'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp', 'characters.sex', 'characters.x', 'characters.y', 'characters.z',
                'characters.exp', 'characters.sp', 'characters.karma', 'characters.pvpkills', 'characters.pkkills',
                'characters.base_class', 'characters.title', 'characters.online', 'characters.onlinetime',
                '(SELECT COUNT(*) FROM characters WHERE clanid = clan_data.clan_id) as ccount',
            ])
            ->from('clan_data')
            ->leftJoin('characters', 'clan_data.leader_id = characters.charId');
    }

    // -----------------------------
    // ПРЕДМЕТЫ
    // -----------------------------
    public function items(): Query
    {
        return (new Query())
            ->select(['owner_id', 'object_id', 'item_id', 'count', 'enchant_level', 'loc', 'loc_data'])
            ->from('items');
    }

    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): bool
    {
        $maxId = (int)$this->db->createCommand('SELECT COALESCE(MAX(object_id), 0) + 1 FROM items')->queryScalar();
        return $this->db->createCommand()
            ->insert('items', [
                'owner_id'      => $ownerId,
                'object_id'     => $maxId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'loc'           => 'INVENTORY',
                'loc_data'      => 0,
                'price_sell'    => 0,
            ])
            ->execute() > 0;
    }

    public function multiInsertItem(array $items): bool
    {
        if (empty($items)) return false;

        $maxId = (int)$this->db->createCommand('SELECT COALESCE(MAX(object_id), 0) + 1 FROM items')->queryScalar();
        $rows = [];
        foreach ($items as $v) {
            $rows[] = [
                $maxId++,
                $v['owner_id'],
                $v['item_id'],
                $v['count'] ?? 1,
                $v['enchant'] ?? 0,
                'INVENTORY',
                0,
                0,
            ];
        }
        return $this->db->createCommand()
            ->batchInsert('items', ['object_id', 'owner_id', 'item_id', 'count', 'enchant_level', 'loc', 'loc_data', 'price_sell'], $rows)
            ->execute() > 0;
    }

    // -----------------------------
    // СТАТИСТИКА
    // -----------------------------
    public function getCountAccounts(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM accounts')->queryScalar();
    }

    public function getCountCharacters(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters')->queryScalar();
    }

    public function getCountOnlineCharacters(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE online = 1')->queryScalar();
    }

    public function getCountRaceHuman(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 0')->queryScalar();
    }

    public function getCountRaceElf(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 1')->queryScalar();
    }

    public function getCountRaceDarkElf(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 2')->queryScalar();
    }

    public function getCountRaceOrk(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 3')->queryScalar();
    }

    public function getCountRaceDwarf(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 4')->queryScalar();
    }

    public function getCountRaceKamael(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE race = 5')->queryScalar();
    }

    public function getCountMen(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE sex = 0')->queryScalar();
    }

    public function getCountWomen(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM characters WHERE sex = 1')->queryScalar();
    }

    public function getCountClans(): int
    {
        return (int)$this->db->createCommand('SELECT COUNT(*) FROM clan_data')->queryScalar();
    }

    public function getTopPvp(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where(['>', 'pvpkills', 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where(['>', 'pkkills', 0])
            ->orderBy(['pkkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTop(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->orderBy(['exp' => SORT_DESC, 'sp' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopRich(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->select([
                'characters.charId AS char_id',
                'characters.char_name',
                'characters.level',
                'characters.account_name',
                'clan_data.clan_name',
                'SUM(items.count) AS adena_count',
            ])
            ->from('characters')
            ->leftJoin('items', 'items.owner_id = characters.charId')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->where(['items.item_id' => 57])
            ->groupBy('characters.charId')
            ->orderBy(['adena_count' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getOnline(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where(['online' => 1])
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopClans(int $limit = 20, int $offset = 0): array
    {
        return $this->clans()
            ->orderBy(['reputation_score' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getCastles(): array
    {
        return (new Query())
            ->select([
                'castle.id', 'castle.name', 'castle.taxPercent', 'castle.siegeDate',
                'clan_data.clan_id', 'clan_data.clan_name', 'clan_data.leader_id',
                'clan_data.clan_level', 'clan_data.hasCastle',
                'clan_data.crest_id', 'clan_data.crest_large_id', 'clan_data.ally_crest_id',
                'clan_data.reputation_score', 'clan_data.ally_id', 'clan_data.ally_name',
            ])
            ->from('castle')
            ->leftJoin('clan_data', 'castle.id = clan_data.hasCastle')
            ->all();
    }

    public function getSiege(): array
    {
        return (new Query())
            ->select([
                'siege_clans.castle_id', 'siege_clans.type', 'clan_data.clan_id',
                'clan_data.clan_name', 'clan_data.leader_id', 'clan_data.clan_level',
                'clan_data.hasCastle', 'clan_data.crest_id', 'clan_data.crest_large_id',
                'clan_data.ally_crest_id', 'clan_data.reputation_score',
                'clan_data.ally_id', 'clan_data.ally_name',
            ])
            ->from('siege_clans')
            ->leftJoin('clan_data', 'siege_clans.clan_id = clan_data.clan_id')
            ->all();
    }

    public function getChronicle(): string
    {
        return 'it';
    }

    public function getField(string $fieldName): ?string
    {
        $map = [
            'accounts.access_level'   => 'accounts.accessLevel',
            'accounts.last_active'    => 'accounts.lastactive',
            'characters.char_id'      => 'characters.charId',
            'clan_data.clan_id'       => 'clan_data.clan_id',
        ];
        return $map[$fieldName] ?? null;
    }

    public function getServerName(): string
    {
        return 'L2J Lovely Interlude';
    }

    // TODO: премиум, HWID, контроль предметов
}