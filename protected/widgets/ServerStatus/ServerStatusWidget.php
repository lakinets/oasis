<?php
namespace app\widgets\ServerStatus;

use yii\base\Widget;
use yii\caching\CacheInterface;
use app\models\Gs;

class ServerStatusWidget extends Widget
{
    /** @var int TTL кэша в минутах */
    public $defaultTtl = 5;

    public function run()
    {
        $cache = \Yii::$app->cache;
        $key   = 'server_status';

        $data = $cache->get($key);
        if ($data === false) {
            $data = [
                'content'     => [],
                'totalOnline' => 0,
            ];

            // используем scope findOpened вместо отсутствующего opened()
            $gsList = Gs::findOpened()->with('ls')->all();

            foreach ($gsList as $gs) {
                try {
                    $online = rand(100, 500); // заглушка

                    $data['content'][$gs->id] = [
                        'gs_status' => $this->ping($gs->ip, $gs->port),
                        'ls_status' => $gs->ls ? $this->ping($gs->ls->ip, $gs->ls->port) : 'offline',
                        'online'    => $online,
                        'gs'        => $gs,
                    ];
                    $data['totalOnline'] += $online;
                } catch (\Exception $e) {
                    $data['content'][$gs->id]['error'] = $e->getMessage();
                }
            }

            $ttl = (int)(\Yii::$app->params['server_status.cache'] ?? $this->defaultTtl);
            if ($ttl > 0) {
                $cache->set($key, $data, $ttl * 60);
            }
        }

        return $this->render('server-status', $data);
    }

    /**
     * Быстрая TCP-проверка порта
     */
    private function ping($ip, $port): string
    {
        $timeout = 1;
        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if ($fp) {
            fclose($fp);
            return 'online';
        }
        return 'offline';
    }
}