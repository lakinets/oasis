<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;

class AsnGuard extends Component implements BootstrapInterface
{
    public $cacheDuration = 3600; // Кэшируем ASN на 1 час

    // Пути, которые НИКОГДА не блокируются
    public $ignoreRoutes = [
        'backend',         // Админка (по умолчанию)
        'install',         // Установка
        'cabinet/deposit', // Вебхуки платежных систем
        'site/error',      // Страница ошибок, чтобы не было рекурсии
        'language',        // Переключение языка
        'gii', 'debug'     // Служебные модули Yii
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
        // 1. Глобальный рубильник
        if (!$this->getConfig('asn_filter.enabled', '0')) {
            return;
        }

        $path = trim(Yii::$app->request->pathInfo, '/');
        
        // 2. Читаем кастомный URL админки на случай, если он изменен
        $adminUrl = $this->getConfig('admin.url', 'backend');
        $routes = $this->ignoreRoutes;
        $routes[] = $adminUrl; // добавляем кастомную админку в игнор

        foreach ($routes as $route) {
            if (strpos($path, $route) === 0) {
                return; // Пропускаем служебные пути
            }
        }

        $ip = Yii::$app->request->userIP;

        // 3. Пропускаем локальные IP
        if ($this->isLocalIp($ip)) {
            return;
        }

        // 4. Проверка на поискового бота
        if ($this->isSearchBot($ip) && $this->isBotAllowed($path)) {
            return;
        }

        // 5. Определяем ASN
        $cacheKey = 'asn_guard_' . md5($ip);
        $asn = Yii::$app->cache->get($cacheKey);

        if ($asn === false) {
            $asn = $this->getAsnByIp($ip);
            Yii::$app->cache->set($cacheKey, $asn ?: 0, $this->cacheDuration);
        }

        // 6. Проверяем ASN
        if ($asn) {
            // Получаем инфу о провайдере напрямую из БД
            $asnInfo = Yii::$app->db->createCommand(
                'SELECT continent_en, country_code, enabled FROM asn_whitelist WHERE asn = :asn'
            )->bindValue(':asn', $asn)->queryOne();

            if ($asnInfo && $asnInfo['enabled'] == 1) {
                // Проверяем блокировки континента и страны
                $contBlocked = Yii::$app->db->createCommand(
                    'SELECT COUNT(*) FROM asn_block_rules WHERE rule_type = "continent" AND rule_value = :val AND enabled = 0'
                )->bindValue(':val', $asnInfo['continent_en'])->queryScalar();

                $countryBlocked = Yii::$app->db->createCommand(
                    'SELECT COUNT(*) FROM asn_block_rules WHERE rule_type = "country" AND rule_value = :val AND enabled = 0'
                )->bindValue(':val', $asnInfo['country_code'])->queryScalar();

                if (!$contBlocked && !$countryBlocked) {
                    return; // Доступ РАЗРЕШЕН
                }
            }
        }

        // 7. Блокировка
        $this->denyAccess();
    }

    private function isLocalIp($ip)
    {
        return $ip === '::1' || 
               strpos($ip, '127.') === 0 || 
               strpos($ip, '192.168.') === 0 || 
               strpos($ip, '10.') === 0 ||
               preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip);
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
        if (!$this->getConfig('asn_filter.search_bots_enabled', '0')) {
            return false;
        }
        if ($path === '') return true; // Главная всегда открыта

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
        // Файл базы должен лежать в protected/data/
        $dbFile = Yii::getAlias('@app/data/GeoLite2-ASN.mmdb');
        if (!file_exists($dbFile)) {
            return null; // Если базы нет, пропускаем
        }
        try {
            $reader = new \GeoIp2\Database\Reader($dbFile);
            $record = $reader->asn($ip);
            return $record->autonomousSystemNumber;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function ipInCidr($ip, $cidr)
    {
        if (strpos($cidr, '/') === false) return $ip === $cidr;
        list($subnet, $mask) = explode('/', $cidr);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    private function denyAccess()
    {
        $code = (int)$this->getConfig('asn_filter.error_code', 500);
        if ($this->getConfig('asn_filter.random_error', '0') === '1') {
            $codes = [403, 404, 500, 502, 503];
            $code = $codes[array_rand($codes)];
        }

        // Очищаем буфер
        while (ob_get_level()) ob_end_clean();
        http_response_code($code);
        
        // Минималистичный ответ (без лишних объяснений для ботов)
        if ($code >= 500) {
            echo "<!DOCTYPE html><html><head><title>Internal Server Error</title></head><body><h1>Internal Server Error</h1></body></html>";
        } elseif ($code == 404) {
            echo "<!DOCTYPE html><html><head><title>Not Found</title></head><body><h1>Not Found</h1></body></html>";
        } else {
            echo "<!DOCTYPE html><html><head><title>Forbidden</title></head><body><h1>Forbidden</h1></body></html>";
        }
        
        Yii::$app->end();
    }

    private function getConfig($param, $default)
    {
        $val = Yii::$app->db->createCommand('SELECT value FROM config WHERE param = :param')
            ->bindValue(':param', $param)
            ->queryScalar();
        return $val !== false ? $val : $default;
    }
}