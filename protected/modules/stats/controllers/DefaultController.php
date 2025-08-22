<?php

namespace app\modules\stats\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use app\models\Gs;
use app\components\Lineage;

class DefaultController extends Controller
{
    /**
     * Единая страница статистики:
     * /stats — берём первый открытый сервер
     * /stats?gs_id=ID — показываем выбранный сервер
     */
    public function actionIndex($gs_id = null)
    {
        // список открытых серверов
        $gsList = [];
        if (method_exists(Gs::class, 'findOpened')) {
            $gsList = Gs::findOpened()->all();
        } else {
            // запасной вариант, если нет findOpened()
            $statusOn = defined(Gs::class . '::STATUS_ON') ? Gs::STATUS_ON : 1;
            $gsList = Gs::find()->where(['status' => $statusOn])->all();
        }

        // сервер по умолчанию — первый открытый
        if ($gs_id === null && !empty($gsList)) {
            $gs_id = $gsList[0]->id;
        }

        $current = $gs_id ? Gs::findOne($gs_id) : null;

        $serverDown = false;
        $online = 0;

        // Данные для трёх секций
        $totalVars = [
            'countAccounts'   => 0,
            'countCharacters' => 0,
            'countClans'      => 0,
            'countOnline'     => 0,
        ];
        $castles = [];
        $clans   = [];

        if ($current) {
            try {
                // подключение
                $lineage = Lineage::gs($current->id)->connect();

                // TTL кеша в секундах
                $ttlMinutes = (int)($current->stats_cache_time ?? 0);
                $ttl = $ttlMinutes > 0 ? $ttlMinutes * 60 : 0;

                // ключи кеша
                $kTotal   = "stats:total:{$current->id}";
                $kCastles = "stats:castles:{$current->id}";
                $kClans   = "stats:clans:{$current->id}";

                // TOTAL
                $totalVars = Yii::$app->cache->get($kTotal);
                if ($totalVars === false) {
                    $totalVars = [
                        'countAccounts'   => (int)$lineage->getCountAccounts(),
                        'countCharacters' => (int)$lineage->getCountCharacters(),
                        'countClans'      => (int)$lineage->getCountClans(),
                        'countOnline'     => (int)$lineage->getCountOnlineCharacters(),
                    ];
                    if ($ttl > 0) {
                        Yii::$app->cache->set($kTotal, $totalVars, $ttl);
                    }
                }
                $online = (int)$totalVars['countOnline'];

                // CASTLES
                $castles = Yii::$app->cache->get($kCastles);
                if ($castles === false) {
                    $castles = (array)$lineage->getCastles();
                    if ($ttl > 0) {
                        Yii::$app->cache->set($kCastles, $castles, $ttl);
                    }
                }

                // CLANS (топ)
                $limit = (int)($current->stats_count_results ?? 20);
                $clans = Yii::$app->cache->get($kClans);
                if ($clans === false) {
                    $clans = (array)$lineage->getTopClans($limit);
                    if ($ttl > 0) {
                        Yii::$app->cache->set($kClans, $clans, $ttl);
                    }
                }
            } catch (\Throwable $e) {
                $serverDown = true;
                Yii::error("Stats error (gs_id={$gs_id}): " . $e->getMessage(), __METHOD__);
            }
        } else {
            $serverDown = true;
        }

        return $this->render('index', [
            'gs_list'    => $gsList,
            'current'    => $current,
            'serverDown' => $serverDown,
            'online'     => $online,
            // секции
            'totalVars'  => $totalVars,
            'castles'    => $castles,
            'clans'      => $clans,
        ]);
    }
}
