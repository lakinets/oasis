<?php
namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\Config;

/**
 * Отдельная вкладка «Платёжные системы».
 * Значения ключей/секретов остаются в таблице `config`.
 * Метаданные шлюзов — в `protected/config/payment_gateways.php`.
 */
class PaymentsController extends BackendController
{
    public function actionIndex()
    {
        $gateways = $this->loadGateways();

        return $this->render('index', [
            'gateways' => $gateways,
        ]);
    }

    public function actionForm($code)
    {
        $gateways = $this->loadGateways();
        if (!isset($gateways[$code])) {
            throw new \yii\web\NotFoundHttpException(Yii::t('backend', 'Платёжная система не найдена.'));
        }
        $gateway = $gateways[$code];

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('Config', []);
            foreach ($postData as $id => $row) {
                $config = Config::findOne((int)$id);
                if ($config !== null && isset($row['value'])) {
                    $val = $row['value'];
                    if (is_array($val)) {
                        $val = json_encode($val);
                    }
                    $config->value = $val;
                    $config->save(false, ['value']);
                }
            }
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Настройки сохранены.'));
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'gateway' => $gateway,
            'code'    => $code,
        ]);
    }

    public function actionToggle($code)
    {
        $gateways = $this->loadGateways();
        if (!isset($gateways[$code])) {
            throw new \yii\web\NotFoundHttpException();
        }
        $param  = $gateways[$code]['enabled_param'];
        $config = Config::findOne(['param' => $param]);
        if ($config !== null) {
            $config->value = $config->value === '1' ? '0' : '1';
            $config->save(false, ['value']);
        }
        return $this->redirect(['index']);
    }

    public function actionToggleTest($code)
    {
        $gateways = $this->loadGateways();
        if (!isset($gateways[$code]) || !$gateways[$code]['supports_test'] || !$gateways[$code]['test_param']) {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Тестовый режим для этого шлюза не поддерживается.'));
            return $this->redirect(['index']);
        }
        $param  = $gateways[$code]['test_param'];
        $config = Config::findOne(['param' => $param]);
        if ($config !== null) {
            $config->value = $config->value === '1' ? '0' : '1';
            $config->save(false, ['value']);
        }
        return $this->redirect(['index']);
    }

    public function actionPing($code)
    {
        $gateways = $this->loadGateways();
        if (!isset($gateways[$code])) {
            throw new \yii\web\NotFoundHttpException();
        }

        $class = $gateways[$code]['class'];
        if (!class_exists($class)) {
            Yii::$app->session->setFlash('error', Yii::t('backend', "Класс {class} не найден.", ['class' => $class]));
            return $this->redirect(['index']);
        }

        $gateway = new $class();

        if (method_exists($gateway, 'validateCredentials')) {
            $result = $gateway->validateCredentials();
            $type = $result['ok'] ? 'success' : 'error';
            Yii::$app->session->setFlash($type, Yii::t('backend', "{name}: {message}", [
                'name' => $gateways[$code]['name'],
                'message' => $result['message']
            ]));
        } else {
            Yii::$app->session->setFlash('info', Yii::t('backend', "Для {name} проверка пока не реализована.", [
                'name' => $gateways[$code]['name']
            ]));
        }

        return $this->redirect(['index']);
    }

    /**
     * Загружает шлюзы из конфига + связанные с ними записи `config`.
     */
    private function loadGateways(): array
    {
        $all = require Yii::getAlias('@app/config/payment_gateways.php');

        foreach ($all as $code => &$gw) {
            $gw['code'] = $code;

            $or = [];
            foreach ((array)$gw['param_prefixes'] as $prefix) {
                $or[] = ['like', 'param', $prefix . '%', false];
            }
            $configs = Config::find()
                ->where(['or', ...$or])
                ->orderBy(['order' => SORT_ASC])
                ->all();
            $gw['configs'] = $configs;

            $enabledCfg = Config::findOne(['param' => $gw['enabled_param']]);
            $gw['is_active'] = $enabledCfg ? ((string)$enabledCfg->value === '1') : false;

            if ($gw['supports_test'] && $gw['test_param']) {
                $testCfg = Config::findOne(['param' => $gw['test_param']]);
                $gw['is_test'] = $testCfg ? ((string)$testCfg->value === '1') : false;
            } else {
                $gw['is_test'] = false;
            }

            // «Настроен» — если хотя бы один секрет заполнен.
            $hasSecret = false;
            foreach ($configs as $c) {
                $leaf = substr($c->param, strrpos($c->param, '.') + 1);
                // Исключаем служебные поля, которые не являются ключами
                if (in_array($leaf, ['enabled', 'test', 'currency', 'price_amount_multiplier', 'exchange_rate'], true)) {
                    continue;
                }
                if (trim((string)$c->value) !== '') {
                    $hasSecret = true;
                    break;
                }
            }
            $gw['is_configured'] = $hasSecret;
        }

        uasort($all, function ($a, $b) {
            return ($a['sort'] ?? 100) <=> ($b['sort'] ?? 100);
        });

        return $all;
    }
}