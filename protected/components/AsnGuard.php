<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;

class AsnGuard extends Component implements BootstrapInterface
{
    public $cacheDuration = 3600;

    public $ignoreRoutes = [
        'install',         
        'backend',         
        'cabinet/deposit', 
        'site/error',      
        'language',        
        'gii', 'debug'     
    ];

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            return;
        }

        $app->on(\yii\web\Application::EVENT_BEFORE_REQUEST, function ($event) {
            $this->checkAccess();
        });
    }

    private function checkAccess()
    {
        $path = trim(Yii::$app->request->pathInfo, '/');

        // 1. Игнорируем установщик
        if (strpos($path, 'install') === 0) {
            return;
        }

        // 2. Проверяем настройки (перехватываем вообще всё через \Throwable)
        try {
            if (!$this->getConfig('asn_filter.enabled', '0')) {
                return;
            }
            $adminUrl = $this->getConfig('admin.url', 'backend');
        } catch (\Throwable $e) {
            return; // Если БД нет — пропускаем
        }

        $routes = $this->ignoreRoutes;
        $routes[] = $adminUrl;

        foreach ($routes as $route) {
            if (strpos($path, $route) === 0) {
                return;
            }
        }

        $ip = Yii::$app->request->userIP;

        if ($this->isLocalIp($ip)) {
            return;
        }

        try {
            // Проверка: если белый список пуст, фильтр не работает (Fail Open)
            $count = Yii::$app->cache->get('asn_whitelist_count');
            if ($count === false) {
                $count = (int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM asn_whitelist')->queryScalar();
                Yii::$app->cache->set('asn_whitelist_count', $count, 300); // 5 минут
            }
            if ($count === 0) {
                return;
            }

            if ($this->isSearchBot($ip) && $this->isBotAllowed($path)) {
                return;
            }

            $cacheKey = 'asn_guard_' . md5($ip);
            $asn = Yii::$app->cache->get($cacheKey);

            if ($asn === false) {
                $asn = $this->getAsnByIp($ip);
                Yii::$app->cache->set($cacheKey, $asn ?: 0, $this->cacheDuration);
            }

            // Если ASN не определился (нет файла базы), пропускаем
            if (!$asn) {
                return; 
            }

            if ($asn) {
                $asnInfo = Yii::$app->db->createCommand(
                    'SELECT continent_en, country_code, enabled FROM asn_whitelist WHERE asn = :asn'
                )->bindValue(':asn', $asn)->queryOne();

                if ($asnInfo && $asnInfo['enabled'] == 1) {
                    $contBlocked = Yii::$app->db->createCommand(
                        'SELECT COUNT(*) FROM asn_block_rules WHERE rule_type = "continent" AND rule_value = :val AND enabled = 0'
                    )->bindValue(':val', $asnInfo['continent_en'])->queryScalar();

                    $countryBlocked = Yii::$app->db->createCommand(
                        'SELECT COUNT(*) FROM asn_block_rules WHERE rule_type = "country" AND rule_value = :val AND enabled = 0'
                    )->bindValue(':val', $asnInfo['country_code'])->queryScalar();

                    if (!$contBlocked && !$countryBlocked) {
                        return; 
                    }
                }
            }
        } catch (\Throwable $e) {
            return; // При любой ошибке БД пропускаем
        }

        $this->denyAccess();
    }

    private function isLocalIp($ip)
    {
        return $ip === '::1' || strpos($ip, '127.') === 0 || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0 || preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip);
    }

    private function isSearchBot($ip)
    {
        $cidrs = Yii::$app->cache->get('bot_cidrs_list');
        if ($cidrs === false) {
            $cidrs = Yii::$app->db->createCommand('SELECT ip_cidr FROM bot_whitelist WHERE enabled = 1')->queryColumn();
            Yii::$app->cache->set('bot_cidrs_list', $cidrs, 3600);
        }
        foreach ($cidrs as $cidr) {
            if ($this->ipInCidr($ip, $cidr)) return true;
        }
        return false;
    }

    private function isBotAllowed($path)
    {
        if (!$this->getConfig('asn_filter.search_bots_enabled', '0')) return false;
        if ($path === '') return true;

        $map = [
            'cabinet'  => 'asn_filter.search_bots_cabinet',
            'stats'    => 'asn_filter.search_bots_stats',
            'page'     => 'asn_filter.search_bots_page',
            'register' => 'asn_filter.search_bots_register',
            'login'    => 'asn_filter.search_bots_login',
        ];

        foreach ($map as $route => $configParam) {
            if (strpos($path, $route) === 0) {
                return (bool)$this->getConfig($configParam, '0');
            }
        }
        return false;
    }

    private function getAsnByIp($ip)
    {
        $dbFile = Yii::getAlias('@app/data/GeoLite2-ASN.mmdb');
        if (!file_exists($dbFile)) return false; // Возвращаем false, если базы нет
        try {
            $reader = new \GeoIp2\Database\Reader($dbFile);
            return $reader->asn($ip)->autonomousSystemNumber;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function ipInCidr($ip, $cidr)
    {
        if (strpos($cidr, '/') === false) return $ip === $cidr;
        list($subnet, $mask) = explode('/', $cidr);
        $maskLong = -1 << (32 - (int)$mask);
        return (ip2long($ip) & $maskLong) === (ip2long($subnet) & $maskLong);
    }

    private function denyAccess()
    {
        $code = (int)$this->getConfig('asn_filter.error_code', 500);
        if ($this->getConfig('asn_filter.random_error', '0') === '1') {
            $code = [403, 404, 500, 502, 503][array_rand([403, 404, 500, 502, 503])];
        }

        while (ob_get_level()) {
            ob_end_clean();
        }
        
        http_response_code($code);
        
        if ($code >= 500) {
            echo "<!DOCTYPE html><html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1></body></html>";
        } elseif ($code == 404) {
            echo "<!DOCTYPE html><html><head><title>Not Found</title></head><body><h1>Not Found</h1></body></html>";
        } else {
            echo "<!DOCTYPE html><html><head><title>Forbidden</title></head><body><h1>Forbidden</h1></body></html>";
        }
        
        exit; // <--- ИСПРАВЛЕНО: Используем exit вместо Yii::$app->end()
    }

    private function getConfig($param, $default)
    {
        try {
            $val = Yii::$app->db->createCommand('SELECT value FROM config WHERE param = :param')
                ->bindValue(':param', $param)
                ->queryScalar();
            return $val !== false ? $val : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}