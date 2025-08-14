<?php

namespace app\modules\stats\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Html;
use yii\db\Query;

class DefaultController extends Controller
{
    // Получение общих данных сервера
    private function getServerData()
    {
        return (new Query())
            ->from('gs')
            ->where(['id' => 1])
            ->one(Yii::$app->db);
    }

    // Подключение к удаленной БД
    private function getRemoteDb($server)
    {
        return new \yii\db\Connection([
            'dsn' => 'mysql:host=' . $server['db_host'] . ';port=' . $server['db_port'] . ';dbname=' . $server['db_name'],
            'username' => $server['db_user'],
            'password' => $server['db_pass'],
            'charset' => 'utf8',
        ]);
    }

    // Форматирование времени в игре
    public function getOnlineTime($time)
    {
        $hours = floor($time / 3600);
        $minutes = floor(($time % 3600) / 60);
        return "{$hours}ч {$minutes}м";
    }

    // Получение названия класса персонажа
    public function getClassName($classId)
    {
        $classNames = [
            0 => 'Warrior',
            1 => 'Mage',
            2 => 'Archer',
            // Добавить все классы игры
        ];
        return $classNames[$classId] ?? 'Unknown';
    }

    public function actionIndex()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $online = $remoteDb
                ->createCommand("SELECT COUNT(*) FROM characters WHERE online = 1")
                ->queryScalar();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('index', [
            'online' => $online,
            'server' => $server,
        ]);
    }

    public function actionClans()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $clans = $remoteDb
                ->createCommand("SELECT * FROM clans ORDER BY level DESC")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('clans', [
            'clans' => $clans,
            'server' => $server,
        ]);
    }

    public function actionCastles()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $castles = $remoteDb
                ->createCommand("SELECT * FROM castles")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('castles', [
            'castles' => $castles,
            'server' => $server,
        ]);
    }

    public function actionClanInfo($clanId)
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $clanInfo = $remoteDb
                ->createCommand("SELECT * FROM clans WHERE id = :id", [':id' => $clanId])
                ->queryOne();

            if (!$clanInfo) {
                return 'Клан не найден';
            }

            $clanCharacters = $remoteDb
                ->createCommand("SELECT * FROM characters WHERE clan_id = :clanId ORDER BY level DESC", [':clanId' => $clanId])
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('clan-info', [
            'clanInfo' => $clanInfo,
            'clanCharacters' => $clanCharacters,
            'server' => $server,
        ]);
    }

    public function actionItems()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $itemsQuery = $remoteDb
                ->createCommand("SELECT * FROM items")
                ->queryAll();

            $items = [];
            foreach ($itemsQuery as $item) {
                $items[$item['item_id']] = [
                    'itemInfo' => $this->getItemInfo($item['item_id']),
                    'maxTotalItems' => $item['total'],
                    'characters' => $this->getCharactersWithItem($item['item_id']),
                ];
            }

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('items', [
            'items' => $items,
            'server' => $server,
        ]);
    }

    public function actionOnline()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $onlinePlayers = $remoteDb
                ->createCommand("SELECT * FROM characters WHERE online = 1 ORDER BY level DESC")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('online', [
            'onlinePlayers' => $onlinePlayers,
            'server' => $server,
        ]);
    }

    public function actionPk()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $pkPlayers = $remoteDb
                ->createCommand("SELECT * FROM characters WHERE pkkills > 0 ORDER BY pkkills DESC")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('pk', [
            'pkPlayers' => $pkPlayers,
            'server' => $server,
        ]);
    }

    public function actionPvp()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $pvpPlayers = $remoteDb
                ->createCommand("SELECT * FROM characters WHERE pvpkills > 0 ORDER BY pvpkills DESC")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('pvp', [
            'pvpPlayers' => $pvpPlayers,
            'server' => $server,
        ]);
    }

    public function actionRich()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $richPlayers = $remoteDb
                ->createCommand("SELECT * FROM characters ORDER BY adena_count DESC LIMIT 50")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('rich', [
            'richPlayers' => $richPlayers,
            'server' => $server,
        ]);
    }

    public function actionTop()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            $topPlayers = $remoteDb
                ->createCommand("SELECT * FROM characters ORDER BY level DESC LIMIT 50")
                ->queryAll();

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('top', [
            'topPlayers' => $topPlayers,
            'server' => $server,
        ]);
    }

    public function actionTotal()
    {
        $server = $this->getServerData();

        if (!$server) {
            return 'Сервер не найден';
        }

        try {
            $remoteDb = $this->getRemoteDb($server);

            // Получаем общие данные
            $countOnline = $remoteDb
                ->createCommand("SELECT COUNT(*) FROM characters WHERE online = 1")
                ->queryScalar();

            $countAccounts = $remoteDb
                ->createCommand("SELECT COUNT(DISTINCT account_id) FROM characters")
                ->queryScalar();

            $countCharacters = $remoteDb
                ->createCommand("SELECT COUNT(*) FROM characters")
                ->queryScalar();

            $countClans = $remoteDb
                ->createCommand("SELECT COUNT(*) FROM clans")
                ->queryScalar();

            $countMen = $remoteDb
                ->createCommand("SELECT COUNT(*) FROM characters WHERE sex = 'male'")
                ->queryScalar();

            $countFemale = $remoteDb
                ->createCommand("SELECT COUNT(*) FROM characters WHERE sex = 'female'")
                ->queryScalar();

            // Получаем данные по расам
            $races = [
                'human' => $remoteDb->createCommand("SELECT COUNT(*) FROM characters WHERE race = 'human'")->queryScalar(),
                'elf' => $remoteDb->createCommand("SELECT COUNT(*) FROM characters WHERE race = 'elf'")->queryScalar(),
                'dark_elf' => $remoteDb->createCommand("SELECT COUNT(*) FROM characters WHERE race = 'dark_elf'")->queryScalar(),
                'ork' => $remoteDb->createCommand("SELECT COUNT(*) FROM characters WHERE race = 'ork'")->queryScalar(),
                'dwarf' => $remoteDb->createCommand("SELECT COUNT(*) FROM characters WHERE race = 'dwarf'")->queryScalar(),
                'kamael' => $remoteDb->createCommand("SELECT COUNT(*) FROM characters WHERE race = 'kamael'")->queryScalar(),
            ];

            $totalRaceCount = array_sum($races);
            $racesPercentage = [];
            foreach ($races as $race => $count) {
                if ($totalRaceCount > 0) {
                    $racesPercentage[$race] = ceil(($count / $totalRaceCount) * 100);
                } else {
                    $racesPercentage[$race] = 0;
                }
            }

        } catch (\Exception $e) {
            return 'Ошибка подключения к серверу: ' . $e->getMessage();
        }

        return $this->render('total', [
            'server' => $server,
            'countOnline' => $countOnline,
            'countAccounts' => $countAccounts,
            'countCharacters' => $countCharacters,
            'countClans' => $countClans,
            'countMen' => $countMen,
            'countFemale' => $countFemale,
            'races' => $races,
            'racesPercentage' => $racesPercentage,
        ]);
    }

    private function getItemInfo($itemId)
    {
        // Логика получения информации о предмете по его ID
        // Пример: из таблицы items_info
        return (new Query())
            ->from('items_info')
            ->where(['id' => $itemId])
            ->one($this->getRemoteDb());
    }

    private function getCharactersWithItem($itemId)
    {
        // Логика получения персонажей с данным предметом
        // Пример: из таблицы characters_items
        return (new Query())
            ->from('characters_items')
            ->where(['item_id' => $itemId])
            ->all($this->getRemoteDb());
    }
}