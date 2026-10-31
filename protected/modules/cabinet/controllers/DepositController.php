<?php
namespace app\modules\cabinet\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\PaymentTransaction;
use app\models\Gs;
use yii\db\Connection;

use app\components\payments\StripeGateway;
use app\components\payments\RobokassaGateway;
use app\components\payments\UnitpayGateway;
use app\components\payments\NOWPaymentsGateway;
use app\components\payments\PayOpGateway;
use app\components\payments\CryptomusGateway;
use app\components\payments\VoletGateway;
use app\components\payments\InterkassaGateway;
use app\components\payments\PaymentsManager;
use app\components\payments\AppConfig;

class DepositController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $callbacks = [
            'stripe-callback', 'robokassa-callback', 'unitpay-callback',
            'nowpayments-callback', 'payop-callback', 'cryptomus-callback',
            'volet-callback', 'interkassa-callback'
        ];
        
        if (in_array($action->id, $callbacks, true)) {
            $this->enableCsrfValidation = false;
        } else {
            $blockedActions = ['index', 'create', 'success', 'fail'];
            if (in_array($action->id, $blockedActions, true) && Gs::isTopupMode()) {
                Yii::$app->session->setFlash('error', Yii::t('views', 'Пополнение баланса отключено.'));
                return $this->redirect(['/cabinet']);
            }
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $providers = PaymentsManager::available();
        return $this->render('index', ['providers' => $providers]);
    }

    public function actionCreate()
    {
        $req      = Yii::$app->request;
        $amount   = (float)$req->post('amount', 0);
        $provider = (string)$req->post('provider', '');

        // Проверка лимитов
        $min = (float)AppConfig::get('payment.min_amount', 10);
        $max = (float)AppConfig::get('payment.max_amount', 100000);

        if ($amount < $min || $amount > $max) {
            Yii::$app->session->setFlash('error', Yii::t('views', "Сумма должна быть от {min} до {max}.", ['min' => $min, 'max' => $max]));
            return $this->redirect(['index']);
        }

        $allowed = array_keys(PaymentsManager::available());
        if ($amount <= 0 || !in_array($provider, $allowed, true)) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Неверные данные.'));
            return $this->redirect(['index']);
        }

        $userId = (int)Yii::$app->user->id;
        $login  = (new \yii\db\Query())
            ->from('users')
            ->select('login')
            ->where(['user_id' => $userId])
            ->scalar();
        if (!$login) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Не удалось определить логин пользователя.'));
            return $this->redirect(['index']);
        }

        // Базовая валюта сайта (берем из конфига, по умолчанию RUB)
        $currency = AppConfig::get('payment.default_currency', 'RUB');

        $tx   = PaymentTransaction::create($provider, $userId, $login, $amount, $currency);
        $desc = Yii::t('views', 'Пополнение баланса');
        $url  = $this->getGateway($provider)
                     ->buildRedirectUrl($tx->order_id, $tx->login, $tx->amount, $tx->currency, $desc);

        return $this->redirect($url);
    }

    /* ... CALLBACKS без изменений ... */

    private function finalizeAndRespond(array $result, string $provider): string
    {
        if (!empty($result['orderId']) && !empty($result['ok'])) {
            $tx = PaymentTransaction::findOne(['order_id' => $result['orderId'], 'provider' => $provider]);

            if ($tx && $tx->status !== 'paid') {
                // =========================================================
                // === ПРОВЕРКА СУММЫ ===
                // =========================================================
                $expectedAmount = (float)$tx->amount;
                $actualAmount   = (float)$result['amount'];
                
                // Допускаем погрешность в 2% (для конвертации валют/комиссий)
                $tolerance = $expectedAmount * 0.02;
                
                if (abs($actualAmount - $expectedAmount) > $tolerance && $expectedAmount > 0) {
                    Yii::error("Payment amount mismatch! Tx: {$tx->order_id}. Expected: {$expectedAmount}, Got: {$actualAmount}", 'deposit');
                    return $result['rawResponse'] ?? 'amount mismatch';
                }
                // =========================================================

                $db = Yii::$app->db;
                $t  = $db->beginTransaction();
                try {
                    $payload = $tx->payload ? json_decode($tx->payload, true) : null;

                    if (is_array($payload) && isset($payload['type']) && $payload['type'] === 'topup') {
                        $success = $this->_deliverTopupItems($payload);
                        if (!$success) {
                            throw new \RuntimeException('Ошибка выдачи предметов. Сервер недоступен.');
                        }
                    } else {
                        $db->createCommand(
                            'UPDATE user_profiles SET balance = balance + :sum WHERE user_id = :uid',
                            [':sum' => $actualAmount, ':uid' => $tx->user_id] // Начисляем фактическую сумму
                        )->execute();
                    }

                    $tx->status    = 'paid';
                    $tx->paid_at   = date('Y-m-d H:i:s');
                    $tx->provider_invoice = $result['externalId'] ?? null;
                    $rawPayload = json_decode($tx->payload, true) ?: [];
                    $rawPayload['callback_data'] = Yii::$app->request->bodyParams ?: Yii::$app->request->get();
                    $tx->payload = json_encode($rawPayload, JSON_UNESCAPED_UNICODE);

                    $tx->save(false);
                    $t->commit();
                } catch (\Throwable $e) {
                    $t->rollBack();
                    Yii::error('Payment finalize error: ' . $e->getMessage(), 'deposit');
                    return $result['rawResponse'] ?? 'internal error';
                }
            }
        }
        return $result['rawResponse'] ?? 'OK';
    }

    private function _deliverTopupItems(array $payload): bool
    {
        $gs_id   = (int)$payload['gs_id'];
        $char_id = (int)$payload['char_id'];
        $item_id = (int)$payload['item_id'];
        $qty     = (int)$payload['total_qty'];

        $gs = Gs::findOne(['id' => $gs_id, 'status' => 1]);
        if (!$gs) return false;

        try {
            $dsn = "mysql:host={$gs->db_host};port={$gs->db_port};dbname={$gs->db_name}";
            $gameDb = new Connection([
                'dsn'      => $dsn,
                'username' => $gs->db_user,
                'password' => method_exists($gs, 'getDbPassDecrypted') ? $gs->getDbPassDecrypted() : $gs->db_pass, // ДЕШИФРУЕМ
                'charset'  => 'utf8',
            ]);

            $className = '\\app\\l2j\\' . ucfirst((string)$gs->version);
            if (!class_exists($className)) return false;
            $driver = new $className($gameDb, $gs->db_name);

            $existing = (new \yii\db\Query())
                ->from($gs->db_name . '.items')
                ->where(['owner_id' => $char_id, 'item_id' => $item_id, 'loc' => 'INVENTORY'])
                ->one($gameDb);

            if ($existing) {
                $gameDb->createCommand()->update(
                    $gs->db_name . '.items',
                    ['count' => (int)$existing['count'] + $qty],
                    ['object_id' => (int)$existing['object_id']]
                )->execute();
            } else {
                $driver->insertItem($char_id, $item_id, $qty, 0);
            }

            return true;
        } catch (\Throwable $e) {
            Yii::error('Topup delivery error: ' . $e->getMessage(), 'deposit');
            return false;
        }
    }

    private function getGateway(string $provider)
    {
        return match ($provider) {
            'stripe'      => new StripeGateway(),
            'robokassa'   => new RobokassaGateway(),
            'unitpay'     => new UnitpayGateway(),
            'nowpayments' => new NOWPaymentsGateway(),
            'payop'       => new PayOpGateway(),
            'cryptomus'   => new CryptomusGateway(),
            'volet'       => new VoletGateway(),
            'interkassa'  => new InterkassaGateway(),
            default       => throw new \RuntimeException('Unknown provider'),
        };
    }
}