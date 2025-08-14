<?php
namespace app\modules\backend\components\versions;

use yii\db\Connection;
use yii\db\Query;

class PwsoftIt
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
                'login' => $login,
                'password' => $password, // уже зашифровано
                'access_level' => $accessLevel,
            ])
            ->execute() > 0;
    }

    public function accounts(): Query
    {
        return (new Query())
            ->select(['login', 'password', 'access_level', 'lastactive as last_active'])
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
                'characters.obj_Id as char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.clanid as clan_id',
                'characters.title',
                'characters.accesslevel as access_level',
                'characters.online',
                'characters.onlinetime',
                'characters.race as base_class',
                'characters.level',
                'characters.exp',
                'characters.sp',
                'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp',
                'characters.maxMp', 'characters.curMp',
                'clan_data.clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id as clan_crest',
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
                'clan_data.clan_id',
                'clan_data.clan_name',
                'clan_data.leader_id',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.crest_id as clan_crest',
                'clan_data.reputation_score',
                '(SELECT COUNT(*) FROM characters WHERE characters.clanid = clan_data.clan_id) as ccount',
                'characters.account_name',
                'characters.obj_Id as char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.karma', 'characters.pvpkills', 'characters.pkkills',
                'characters.title', 'characters.accesslevel as access_level',
                'characters.online', 'characters.onlinetime',
                'characters.race as base_class', 'characters.level', 'characters.exp',
                'characters.sp', 'characters.maxHp', 'characters.curHp',
                'characters.maxCp', 'characters.curCp', 'characters.maxMp', 'characters.curMp',
                'ally_name', 'ally_crest_id as ally_crest', 'ally_id',
            ])
            ->from('clan_data')
            ->leftJoin('characters', 'characters.obj_Id = clan_data.leader_id');
    }

    // -----------------------------
    // ПРЕДМЕТЫ
    // -----------------------------

    public function items(): Query
    {
        return (new Query())
            ->select([
                'owner_id', 'object_id', 'item_id',
                'count', 'enchant_level', 'loc', 'loc_data',
            ])
            ->from('items');
    }

    public function insertItem(int $ownerId, int $itemId, int $count = 1, int $enchantLevel = 0): bool
    {
        $maxId = $this->db->createCommand('SELECT COALESCE(MAX(object_id), 0) + 1 FROM items')->queryScalar();

        return $this->db->createCommand()
            ->insert('items', [
                'owner_id' => $ownerId,
                'object_id' => $maxId,
                'item_id' => $itemId,
                'count' => $count,
                'enchant_level' => $enchantLevel,
                'loc' => 'INVENTORY',
            ])
            ->execute() > 0;
    }

    public function multiInsertItem(array $items): bool
    {
        if (empty($items)) return false;

        $maxId = $this->db->createCommand('SELECT COALESCE(MAX(object_id), 0) + 1 FROM items')->queryScalar();
        foreach ($items as &$item) {
            $item['object_id'] = $maxId++;
            $item['enchant_level'] = $item['enchant'] ?? 0;
            $item['loc'] = 'INVENTORY';
            unset($item['enchant']);
        }

        return $this->db->createCommand()
            ->batchInsert('items', ['owner_id', 'object_id', 'item_id', 'count', 'enchant_level', 'loc'], $items)
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

    public function getTopPvp(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where(['>', 'pvpkills', 0])
            ->andWhere(['accesslevel' => 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->where(['>', 'pkkills', 0])
            ->andWhere(['accesslevel' => 0])
            ->orderBy(['pkkills' =>