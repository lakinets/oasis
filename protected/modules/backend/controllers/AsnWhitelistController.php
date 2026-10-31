<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use app\modules\backend\models\AsnWhitelist;
use app\modules\backend\models\AsnBlockRule;
use app\modules\backend\models\Config;

class AsnWhitelistController extends BackendController
{
    private const ALLOWED_ERROR_CODES = ['403', '404', '500', '502', '503'];

    private function getConfigValue(string $param, $default = null)
    {
        $c = Config::find()->where(['param' => $param])->one();
        return $c ? (string)$c->value : $default;
    }

    /**
     * Прямая запись в БД с правильным форматом даты
     */
    private function setConfigValue(string $param, $value): void
    {
        $value = (string)$value;
        $exists = (new \yii\db\Query())->from('config')->where(['param' => $param])->exists();
        
        if ($exists) {
            Yii::$app->db->createCommand()
                ->update('config', ['value' => $value], ['param' => $param])
                ->execute();
        } else {
            $now = date('Y-m-d H:i:s'); // Формат для DATETIME
            Yii::$app->db->createCommand()
                ->insert('config', [
                    'param'      => $param,
                    'value'      => $value,
                    'default'    => $value,
                    'label'      => $param,
                    'group_id'   => 0,
                    'order'      => 0,
                    'method'     => '',
                    'field_type' => 'string',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->execute();
        }
    }

    private function getAdminAsn(): ?int {
        $ip = Yii::$app->request->userIP;
        if (!$ip || $ip === '::1' || strpos($ip, '127.0.0.1') === 0 || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return null;
        }

        $cacheKey = 'admin_asn_' . md5($ip);
        $asn = Yii::$app->cache->get($cacheKey);
        if ($asn !== false) {
            return $asn > 0 ? (int)$asn : null;
        }

        $ch = curl_init("http://ip-api.com/json/{$ip}?fields=as");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['as']) && preg_match('/^AS(\d+)/i', $data['as'], $matches)) {
                $asnNum = (int)$matches[1];
                Yii::$app->cache->set($cacheKey, $asnNum, 86400);
                $this->setConfigValue('asn_filter.admin_asn', $asnNum);
                return $asnNum;
            }
        }
        
        Yii::$app->cache->set($cacheKey, 0, 3600);
        return null;
    }

    private function buildTree(): array
    {
        $rows = (new \yii\db\Query())
            ->select([
                'continent_en', 'continent_ru',
                'country_code', 'country_en', 'country_ru',
                'asn_count' => 'COUNT(*)',
                'blocked_count' => 'SUM(CASE WHEN enabled = 0 THEN 1 ELSE 0 END)'
            ])
            ->from('asn_whitelist')
            ->groupBy(['continent_en', 'continent_ru', 'country_code', 'country_en', 'country_ru'])
            ->orderBy(['continent_en' => SORT_ASC, 'country_en' => SORT_ASC])
            ->all();

        $tree = [];
        foreach ($rows as $row) {
            $contKey = $row['continent_en'];
            if (!isset($tree[$contKey])) {
                $tree[$contKey] = [
                    'name_en'       => $row['continent_en'],
                    'name_ru'       => $row['continent_ru'],
                    'countries'     => [],
                    'blocked_count' => 0
                ];
            }
            
            $blocked = (int)$row['blocked_count'];
            $tree[$contKey]['blocked_count'] += $blocked;

            $tree[$contKey]['countries'][$row['country_code']] = [
                'code'          => $row['country_code'],
                'name_en'       => $row['country_en'],
                'name_ru'       => $row['country_ru'],
                'asn_count'     => (int)$row['asn_count'],
                'blocked_count' => $blocked,
            ];
        }
        return $tree;
    }

    public function actionIndex()
    {
        $tree = $this->buildTree();
        $adminAsn = $this->getAdminAsn();

        $continentRules = AsnBlockRule::find()->where(['rule_type' => AsnBlockRule::TYPE_CONTINENT])->indexBy('rule_value')->all();
        $countryRules = AsnBlockRule::find()->where(['rule_type' => AsnBlockRule::TYPE_COUNTRY])->indexBy('rule_value')->all();

        $totalAsns = (int)AsnWhitelist::find()->count();
        $totalEnabled = (int)AsnWhitelist::find()->where(['enabled' => 1])->count();
        $totalBlocked = $totalAsns - $totalEnabled;

        return $this->render('index', [
            'tree'           => $tree,
            'continentRules' => $continentRules,
            'countryRules'   => $countryRules,
            'globalEnabled'  => (int)$this->getConfigValue('asn_filter.enabled', '0'),
            'errorCode'      => (string)$this->getConfigValue('asn_filter.error_code', '500'),
            'randomError'    => (int)$this->getConfigValue('asn_filter.random_error', '0'),
            'totalAsns'      => $totalAsns,
            'totalEnabled'   => $totalEnabled,
            'totalBlocked'   => $totalBlocked,
            'allowedCodes'   => self::ALLOWED_ERROR_CODES,
            'adminAsn'       => $adminAsn,
            
            // SEO настройки
            'botsEnabled'    => (int)$this->getConfigValue('asn_filter.search_bots_enabled', '0'),
            'botCabinet'     => (int)$this->getConfigValue('asn_filter.search_bots_cabinet', '0'),
            'botStats'       => (int)$this->getConfigValue('asn_filter.search_bots_stats', '0'),
            'botPage'        => (int)$this->getConfigValue('asn_filter.search_bots_page', '0'),
            'botRegister'    => (int)$this->getConfigValue('asn_filter.search_bots_register', '0'),
            'botLogin'       => (int)$this->getConfigValue('asn_filter.search_bots_login', '0'),
        ]);
    }

    public function actionSetGlobal()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $value = (int)Yii::$app->request->post('value', 0);
        $this->setConfigValue('asn_filter.enabled', $value ? 1 : 0);
        return ['ok' => true, 'value' => $value];
    }

    public function actionSetErrorCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $code   = (string)Yii::$app->request->post('code', '500');
        $random = (int)Yii::$app->request->post('random', 0);
        if (!in_array($code, self::ALLOWED_ERROR_CODES, true)) $code = '500';
        $this->setConfigValue('asn_filter.error_code', $code);
        $this->setConfigValue('asn_filter.random_error', $random ? 1 : 0);
        return ['ok' => true, 'code' => $code, 'random' => $random ? 1 : 0];
    }

    public function actionSetSearchBots()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $param = (string)Yii::$app->request->post('param', '');
        $value = (int)Yii::$app->request->post('value', 0);
        
        $allowed = [
            'asn_filter.search_bots_enabled',
            'asn_filter.search_bots_cabinet',
            'asn_filter.search_bots_stats',
            'asn_filter.search_bots_page',
            'asn_filter.search_bots_register',
            'asn_filter.search_bots_login'
        ];

        if (in_array($param, $allowed, true)) {
            $this->setConfigValue($param, $value ? 1 : 0);
            return ['ok' => true, 'param' => $param, 'value' => $value];
        }

        throw new BadRequestHttpException('Invalid param');
    }

    public function actionToggleContinent() { return $this->toggleRule(AsnBlockRule::TYPE_CONTINENT); }
    public function actionToggleCountry() { return $this->toggleRule(AsnBlockRule::TYPE_COUNTRY); }

    private function toggleRule(string $type): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $value   = (string)Yii::$app->request->post('value', '');
        $enabled = (int)Yii::$app->request->post('enabled', 1) ? 1 : 0;
        $adminAsn = $this->getAdminAsn();

        if ($value === '') throw new BadRequestHttpException('Empty rule value');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $rule = AsnBlockRule::findOne(['rule_type' => $type, 'rule_value' => $value]);
            if ($rule === null) {
                $rule = new AsnBlockRule();
                $rule->rule_type  = $type;
                $rule->rule_value = $value;
            }
            $rule->enabled    = $enabled;
            $rule->updated_at = time();
            $rule->save(false);

            if ($type === AsnBlockRule::TYPE_CONTINENT) {
                $countries = AsnWhitelist::find()->select('country_code')->where(['continent_en' => $value])->distinct()->column();

                foreach ($countries as $code) {
                    $cRule = AsnBlockRule::findOne(['rule_type' => AsnBlockRule::TYPE_COUNTRY, 'rule_value' => $code]);
                    if ($cRule === null) {
                        $cRule = new AsnBlockRule();
                        $cRule->rule_type  = AsnBlockRule::TYPE_COUNTRY;
                        $cRule->rule_value = $code;
                    }
                    $cRule->enabled    = $enabled;
                    $cRule->updated_at = time();
                    $cRule->save(false);

                    $condition = ['country_code' => $code];
                    if ($adminAsn && $enabled === 0) {
                        $condition = ['and', ['country_code' => $code], ['!=', 'asn', $adminAsn]];
                    }
                    Yii::$app->db->createCommand()->update('asn_whitelist', ['enabled' => $enabled], $condition)->execute();
                }
            } elseif ($type === AsnBlockRule::TYPE_COUNTRY) {
                $condition = ['country_code' => $value];
                if ($adminAsn && $enabled === 0) {
                    $condition = ['and', ['country_code' => $value], ['!=', 'asn', $adminAsn]];
                }
                Yii::$app->db->createCommand()->update('asn_whitelist', ['enabled' => $enabled], $condition)->execute();
            }

            if ($adminAsn && $enabled === 0) {
                Yii::$app->db->createCommand()->update('asn_whitelist', ['enabled' => 1], ['asn' => $adminAsn])->execute();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            throw new \yii\web\ServerErrorHttpException('Transaction failed');
        }

        return ['ok' => true, 'value' => $value, 'enabled' => $enabled];
    }

    public function actionToggleAsn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $asn     = (int)Yii::$app->request->post('asn', 0);
        $enabled = (int)Yii::$app->request->post('enabled', 1) ? 1 : 0;

        $adminAsn = $this->getAdminAsn();
        if ($adminAsn && $asn === $adminAsn && $enabled === 0) {
            return ['ok' => false, 'error' => 'Вы не можете заблокировать свой собственный ASN.'];
        }

        $row = AsnWhitelist::findOne(['asn' => $asn]);
        if ($row === null) throw new BadRequestHttpException('ASN not found');
        $row->updateAttributes(['enabled' => $enabled]);

        return ['ok' => true, 'asn' => $asn, 'enabled' => $enabled];
    }

    public function actionLoadCountryAsns()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $code = (string)Yii::$app->request->get('code', '');
        $rows = AsnWhitelist::find()->where(['country_code' => $code])->orderBy(['asn' => SORT_ASC])->all();
        return ['ok' => true, 'items' => $this->mapAsnRows($rows)];
    }

    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q = trim((string)Yii::$app->request->get('q', ''));
        if (mb_strlen($q) < 2) return ['ok' => true, 'items' => []];

        $rows = AsnWhitelist::find()->where(['or',
            ['like', 'description', $q], ['like', 'asn', $q],
            ['like', 'country_ru', $q], ['like', 'country_en', $q],
            ['like', 'country_code', $q], ['like', 'continent_ru', $q], ['like', 'continent_en', $q],
        ])->orderBy(['asn' => SORT_ASC])->limit(200)->all();

        return ['ok' => true, 'items' => $this->mapAsnRows($rows)];
    }

    public function actionShowBlocked()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $rows = AsnWhitelist::find()->where(['enabled' => 0])->orderBy(['continent_en' => SORT_ASC, 'country_code' => SORT_ASC, 'asn' => SORT_ASC])->limit(500)->all();
        return ['ok' => true, 'items' => $this->mapAsnRows($rows)];
    }

    private function mapAsnRows($rows): array
    {
        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                'asn'          => (int)$r->asn,
                'description'  => (string)$r->description,
                'country_code' => (string)$r->country_code,
                'country_en'   => (string)$r->country_en,
                'country_ru'   => (string)$r->country_ru,
                'continent_en' => (string)$r->continent_en,
                'continent_ru' => (string)$r->continent_ru,
                'enabled'      => (int)$r->enabled,
            ];
        }
        return $items;
    }
}