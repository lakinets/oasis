<?php
namespace app\modules\backend\components\versions;

use yii\db\Connection;
use yii\db\Query;

class EmurtHf
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
                'password'     => $password,
                'access_level' => $accessLevel,
                'bonus'        => 0,
            ])
            ->execute() > 0;
    }

    public function accounts(): Query
    {
        return (new Query())
            ->select(['login', 'password', 'last_access AS last_active', 'access_level'])
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
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'characters.sex',
                'characters.x', 'characters.y', 'characters.z',
                'characters.karma',
                'characters.pvpkills',
                'characters.pkkills',
                'characters.clanid AS clan_id',
                'characters.title',
                'characters.online',
                'characters.onlinetime',
                'character_subclasses.class_id AS base_class',
                'character_subclasses.level',
                'character_subclasses.exp',
                'character_subclasses.sp',
                'character_subclasses.maxHp',
                'character_subclasses.maxMp',
                'character_subclasses.maxCp',
                'character_subclasses.curHp',
                'character_subclasses.curMp',
                'character_subclasses.curCp',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFortress AS hasFort',
                'clan_data.crest AS clan_crest',
                'clan_data.reputation_score',
                'clan_subpledges.name AS clan_name',
                'characters.accesslevel AS access_level',
                'IF(cv.value > 0, 1, 0) AS jail',
            ])
            ->from('characters')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('character_variables cv', 'cv.obj_id = characters.obj_Id AND cv.name = "jailed"');
    }

    // -----------------------------
    // КЛАНЫ
    // -----------------------------
    public function clans(): Query
    {
        return (new Query())
            ->select([
                'clan_data.clan_id',
                'clan_subpledges.name AS clan_name',
                'clan_data.clan_level',
                'clan_data.hasCastle',
                'clan_data.hasFortress AS hasFort',
                'clan_data.ally_id',
                'ally_data.ally_name',
                'clan_subpledges.leader_id',
                'clan_data.crest AS clan_crest',
                'clan_data.largecrest AS clan_crest_large',
                'ally_data.crest AS ally_crest',
                'clan_data.reputation_score',
                'characters.char_name',
                'characters.account_name',
                'characters.obj_Id AS char_id',
                'character_subclasses.level',
                'character_subclasses.curHp',
                'character_subclasses.curMp',
                'character_subclasses.curCp',
                'character_subclasses.maxHp',
                'character_subclasses.maxMp',
                'character_subclasses.maxCp',
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
                'characters.accesslevel AS access_level',
                '(SELECT COUNT(*) FROM characters WHERE clanid = clan_data.clan_id) AS ccount',
            ])
            ->from('clan_data')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->leftJoin('characters', 'clan_subpledges.leader_id = characters.obj_Id')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1')
            ->andWhere('character_subclasses.isBase = 1');
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
        return $this->db->createCommand()
            ->insert('items_delayed', [
                'owner_id'      => $ownerId,
                'item_id'       => $itemId,
                'count'         => $count,
                'enchant_level' => $enchantLevel,
                'description'   => 'GHTWEB',
            ])
            ->execute() > 0;
    }

    public function multiInsertItem(array $items): bool
    {
        if (empty($items)) return false;

        $rows = [];
        foreach ($items as $v) {
            $rows[] = [
                $v['owner_id'] ?? 0,
                $v['item_id'] ?? 0,
                $v['count'] ?? 1,
                $v['enchant'] ?? 0,
                'GHTWEB',
            ];
        }

        return $this->db->createCommand()
            ->batchInsert('items_delayed', ['owner_id', 'item_id', 'count', 'enchant_level', 'description'], $rows)
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

    private function getCountRaceById(int $id): int
    {
        $classes = \Yii::$app->params['l2']['classList'] ?? [];
        $ids     = array_keys(array_filter($classes, fn($c) => $c['race'] === $id));
        if (empty($ids)) return 0;

        return (int)$this->db->createCommand()
            ->select('COUNT(*)')
            ->from('character_subclasses')
            ->where(['in', 'class_id', $ids])
            ->andWhere(['isBase' => 1])
            ->queryScalar();
    }

    public function getCountRaceHuman(): int { return $this->getCountRaceById(0); }
    public function getCountRaceElf(): int    { return $this->getCountRaceById(1); }
    public function getCountRaceDarkElf(): int { return $this->getCountRaceById(2); }
    public function getCountRaceOrk(): int    { return $this->getCountRaceById(3); }
    public function getCountRaceDwarf(): int  { return $this->getCountRaceById(4); }
    public function getCountRaceKamael(): int { return $this->getCountRaceById(5); }

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
            ->andWhere(['>', 'pvpkills', 0])
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['pvpkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopPk(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->andWhere(['>', 'pkkills', 0])
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['pkkills' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTop(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['exp' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopRich(int $limit = 20, int $offset = 0): array
    {
        return (new Query())
            ->select([
                'characters.obj_Id AS char_id',
                'characters.char_name',
                'characters.level',
                'characters.account_name',
                'clan_subpledges.name AS clan_name',
                'SUM(items.count) AS adena_count',
            ])
            ->from('characters')
            ->leftJoin('items', 'items.owner_id = characters.obj_Id')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->where(['items.item_id' => 57])
            ->andWhere(['characters.accesslevel' => 0])
            ->groupBy('characters.obj_Id')
            ->orderBy(['adena_count' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getOnline(int $limit = 20, int $offset = 0): array
    {
        return $this->characters()
            ->andWhere(['online' => 1])
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['level' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getTopClans(int $limit = 20, int $offset = 0): array
    {
        return $this->clans()
            ->andWhere(['characters.accesslevel' => 0])
            ->orderBy(['reputation_score' => SORT_DESC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function getCastles(): array
    {
        return (new Query())
            ->select([
                'castle.id',
                'castle.name',
                'castle.tax_percent AS taxPercent',
                'castle.siege_date AS siegeDate',
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
                'ally_data.crest AS ally_crest',
            ])
            ->from('castle')
            ->leftJoin('clan_data', 'castle.id = clan_data.hasCastle')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->all();
    }

    public function getSiege(): array
    {
        return (new Query())
            ->select([
                'siege_clans.residence_id AS castle_id',
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
                'ally_data.crest AS ally_crest',
            ])
            ->from('siege_clans')
            ->leftJoin('clan_data', 'siege_clans.clan_id = clan_data.clan_id')
            ->leftJoin('clan_subpledges', 'clan_data.clan_id = clan_subpledges.clan_id AND clan_subpledges.type = 0')
            ->leftJoin('ally_data', 'clan_data.ally_id = ally_data.ally_id')
            ->all();
    }

    public function getChronicle(): string
    {
        return 'hf';
    }

    public function getField(string $fieldName): ?string
    {
        return [
            'accounts.access_level'   => 'accounts.access_level',
            'accounts.last_active'    => 'accounts.last_access',
            'characters.char_id'      => 'characters.obj_Id',
            'characters.access_level' => 'characters.accesslevel',
            'clan_data.clan_id'       => 'clan_data.clan_id',
        ][$fieldName] ?? null;
    }

    public function getServerName(): string
    {
        return 'Emurt High Five';
    }
}