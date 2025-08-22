<?php

namespace app\l2j;

use Yii;
use yii\db\Connection;
use yii\db\Command;
use app\models\AllItems;

class OpenTeam_hf
{
    private Connection $_db;
    private $_context;

    private array $_fields = [
        'accounts.access_level'   => 'accounts.access_level',
        'characters.access_level' => 'characters.accesslevel',
        'characters.char_id'      => 'characters.obj_Id',
        'clan_data.clan_id'       => 'clan_data.clan_id',
    ];

    public function __construct($context)
    {
        $this->_context = $context;
        $this->_db = $context->getDb(); // должен вернуть Yii::$app->db
    }

    public function insertAccount(string $login, string $password, int $access_level = 0): int
    {
        return $this->_db->createCommand()
            ->insert('accounts', [
                'login'        => $login,
                'password'     => $this->_context->passwordEncrypt($password),
                'access_level' => $access_level,
            ])
            ->execute();
    }

    public function accounts(?Command $command = null): Command
    {
        if (!$command) {
            $command = $this->_db->createCommand();
        }

        return $command
            ->select(['login', 'password', 'lastactive AS last_active', 'access_level'])
            ->from('accounts');
    }

    public function characters(?Command $command = null): Command
    {
        if (!$command) {
            $command = $this->_db->createCommand();
        }

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
                'clan_data.clan_name',
                'characters.accesslevel AS access_level',
                '(SELECT IF(value>0,1,0) FROM character_variables WHERE character_variables.obj_id = characters.obj_Id AND character_variables.name = "jailed" LIMIT 1) as jail'
            ])
            ->from('characters')
            ->leftJoin('character_subclasses', 'characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1')
            ->leftJoin('clan_data', 'characters.clanid = clan_data.clan_id');
    }

    public function getCountAccounts(): int
    {
        return (int)$this->_db->createCommand("SELECT COUNT(*) FROM accounts")->queryScalar();
    }

    public function getCountCharacters(): int
    {
        return (int)$this->_db->createCommand("SELECT COUNT(*) FROM characters")->queryScalar();
    }

    public function getCountOnlineCharacters(): int
    {
        return (int)$this->_db->createCommand("SELECT COUNT(*) FROM characters WHERE online = 1")->queryScalar();
    }

    public function getPremiumInfo(string $accountName): array
    {
        $res = $this->_db->createCommand("SELECT * FROM accounts WHERE login = :login AND bonus_expire > 0 LIMIT 1")
            ->bindValue(':login', $accountName)
            ->queryOne();

        return [
            'dateEnd' => $res && $res['bonus_expire'] > 0 ? (int)substr((string)$res['bonus_expire'], 0, 10) : 0,
        ];
    }

    public function addPremium(string $accountName, int $timeEnd): int
    {
        return $this->_db->createCommand()
            ->update('accounts', ['bonus_expire' => $timeEnd], 'login = :login', [':login' => $accountName])
            ->execute();
    }

    public function getItemsControl(array $itemsIds): array
    {
        if (empty($itemsIds)) {
            return [];
        }

        $items = AllItems::findAll(['item_id' => $itemsIds]);
        $itemNames = [];
        foreach ($items as $item) {
            $itemNames[$item->item_id] = $item;
        }

        $res = $this->_db->createCommand("
            SELECT
                MAX(items.count) AS maxCountItems,
                COUNT(items.count) AS countItems,
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
                characters.pvpkills,
                characters.pkkills,
                characters.clanid AS clan_id,
                characters.title,
                characters.online,
                characters.onlinetime,
                clan_data.clan_name,
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
                character_subclasses.maxCp
            FROM items
            LEFT JOIN characters ON items.owner_id = characters.obj_Id
            LEFT JOIN clan_data ON characters.clanid = clan_data.clan_id
            LEFT JOIN character_subclasses ON characters.obj_Id = character_subclasses.char_obj_id AND character_subclasses.isBase = 1
            WHERE items.item_id IN (" . implode(',', $itemsIds) . ")
            GROUP BY items.owner_id, items.item_id
            ORDER BY maxCountItems DESC
        ")->queryAll();

        // дальше обработка $res и $itemNames...

        return []; // верни результат по аналогии
    }
}