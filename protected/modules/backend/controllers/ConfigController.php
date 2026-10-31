<?php
namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\ConfigGroup;
use app\modules\backend\models\Config;

class ConfigController extends BackendController
{
    /**
     * Префиксы параметров, которые управляются в отдельной вкладке
     * «Платёжные системы» (/backend/payments).
     */
    private const PAYMENT_PREFIXES = [
        'robokassa.',
        'unitpay.',
        'nowpayments.',
        'payop.',
        'cryptomus.',
        'volet.',
        'interkassa.',
        'stripe.',
        'payment.robokassa.',
        'payment.unitpay.',
        'payment.nowpayments.',
        'payment.payop.',
        'payment.cryptomus.',
        'payment.volet.',
        'payment.interkassa.',
        'payment.stripe.',
    ];

    /**
     * Префиксы параметров, которые управляются в других вкладках админки.
     * Такие параметры не должны появляться в общих настройках,
     * чтобы не редактировать одно и то же в двух местах.
     */
    private const EXCLUDED_PREFIXES = [
        'asn_filter.', // -> /backend/asn-whitelist
    ];

    public function actionIndex()
    {
        $groups = ConfigGroup::findOpened()
            ->orderBy(['order' => SORT_ASC])
            ->with('configs')
            ->all();

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post('Config', []);
            if (!empty($postData)) {
                $newAdminUrl = null;
                $reserved = ['cabinet', 'login', 'logout', 'register', 'stats', 'news', 'gallery', 'page', 'site', 'install', 'backend'];

                foreach ($postData as $id => $row) {
                    $config = Config::findOne($id);
                    if ($config !== null && isset($row['value'])) {
                        $val = $row['value'];

                        if (is_array($val)) {
                            $val = json_encode($val);
                        }

                        if ($config->param === 'admin.url') {
                            $val = trim($val);
                            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $val)) {
                                Yii::$app->session->setFlash('error', 'URL админ-панели может содержать только буквы, цифры, дефис и подчеркивание.');
                                return $this->refresh();
                            }
                            if (in_array(strtolower($val), $reserved, true) && $val !== 'backend') {
                                Yii::$app->session->setFlash('error', 'Этот URL зарезервирован системой. Выберите другой.');
                                return $this->refresh();
                            }
                            if ($val !== $config->value) {
                                $newAdminUrl = $val;
                            }
                        }

                        $config->value = $val;
                        $config->save(false, ['value']);
                    }
                }

                Yii::$app->session->setFlash('success', Yii::t('backend', 'Настройки успешно сохранены.'));

                if ($newAdminUrl !== null) {
                    Yii::$app->session->setFlash('info', 'URL админ-панели изменен. Новый адрес: /' . $newAdminUrl);
                    return $this->redirect('/' . $newAdminUrl . '/config');
                }
            }
            return $this->refresh();
        }

        // Исключаем параметры, которыми управляют другие вкладки
        $isExcluded = function ($param) {
            foreach (self::PAYMENT_PREFIXES as $prefix) {
                if (strpos($param, $prefix) === 0) {
                    return true;
                }
            }
            foreach (self::EXCLUDED_PREFIXES as $prefix) {
                if (strpos($param, $prefix) === 0) {
                    return true;
                }
            }
            return false;
        };

        foreach ($groups as $group) {
            $filtered = array_values(array_filter(
                $group->configs,
                fn($c) => !$isExcluded($c->param)
            ));
            $group->populateRelation('configs', $filtered);
        }

        // Убираем группы, оставшиеся без конфигов
        $groups = array_values(array_filter($groups, fn($g) => !empty($g->configs)));

        return $this->render('index', [
            'groups' => $groups,
        ]);
    }
}