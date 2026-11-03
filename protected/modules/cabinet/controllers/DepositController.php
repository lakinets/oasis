<?php
namespace app\modules\cabinet\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\PaymentTransaction;
use app\models\Gs;
use yii\db\Connection;
use yii\db\Expression;

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
                    // 1. Разрешаем доступ ВСЕМ (включая гостей) к колбэкам платежных систем
                    [
                        'actions' => [
                            'stripe-callback', 'robokassa-callback', 'unitpay-callback',
                            'nowpayments-callback', 'payop-callback', 'cryptomus-callback',
                            'volet-callback', 'interkassa-callback'
                        ],
                        'allow' => true,
                        'roles' => ['?'], // '?' означает "гость" (без авторизации)
                    ],
                    // 2. Для всех остальных действий (страницы пополнения) требуем вход
                    [
                        'allow' => true,
                        'roles' => ['@'], // '@' означает "авторизованный пользователь"
                    ],
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
            // Блокируем только index и create, если включен режим магазина.
            $blockedActions = ['index', 'create']; 
            
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

    /* ----------------- CALLBACKS ----------------- */

    public function actionStripeCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('stripe')->handleCallback(Yii::$app->request),
            'stripe'
        );
    }

    public function actionRobokassaCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('robokassa')->handleCallback(Yii::$app->request),
            'robokassa'
        );
    }

    public function actionUnitpayCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('unitpay')->handleCallback(Yii::$app->request),
            'unitpay'
        );
    }

    public function actionNowpaymentsCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('nowpayments')->handleCallback(Yii::$app->request),
            'nowpayments'
        );
    }

    public function actionPayopCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('payop')->handleCallback(Yii::$app->request),
            'payop'
        );
    }

    public function actionCryptomusCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('cryptomus')->handleCallback(Yii::$app->request),
            'cryptomus'
        );
    }

    public function actionVoletCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('volet')->handleCallback(Yii::$app->request),
            'volet'
        );
    }

    public function actionInterkassaCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return $this->finalizeAndRespond(
            $this->getGateway('interkassa')->handleCallback(Yii::$app->request),
            'interkassa'
        );
    }

    public function actionSuccess(string $provider = null, string $orderId = null)
    {
        return $this->render('success', ['provider' => $provider, 'orderId' => $orderId]);
    }

    public function actionFail(string $provider = null, string $orderId = null, string $msg = null)
    {
        return $this->render('fail', ['provider' => $provider, 'orderId' => $orderId, 'msg' => $msg]);
    }

    private function finalizeAndRespond(array $result, string $provider): string
    {
        if (!empty($result['orderId']) && !empty($result['ok'])) {
            $tx = PaymentTransaction::findOne(['order_id' => $result['orderId'], 'provider' => $provider]);

            if ($tx && $tx->status !== 'paid') {
                // =========================================================
                // === УМНОЕ ЗАЧИСЛЕНИЕ (КОМИСССИЮ БЕРЕМ НА СЕБЯ) ===
                // =========================================================
                $expectedAmount = (float)$tx->amount;
                $actualAmount   = (float)$result['amount'];
                
                // Если шлюз удержал комиссию (пришло меньше), мы просто пишем в лог,
                // но НЕ отменяем платеж. Игрок получит ровно то, что покупал.
                if ($actualAmount < $expectedAmount && $actualAmount > 0) {
                    $fee = $expectedAmount - $actualAmount;
                    Yii::info("Payment fee absorbed. Tx: {$tx->order_id}. Expected: {$expectedAmount}, Got: {$actualAmount}. Fee: {$fee}", 'deposit');
                }
                
                // Всегда начисляем ровно ту сумму/количество, которые запросил игрок.
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
                        // Для обычного пополнения баланса начисляем $expectedAmount (как просил игрок)
                        $db->createCommand(
                            'UPDATE user_profiles SET balance = balance + :sum WHERE user_id = :uid',
                            [':sum' => $expectedAmount, ':uid' => $tx->user_id]
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
                'password' => $gs->db_pass, // Пароль берем как есть
                'charset'  => 'utf8',
            ]);

            Yii::info("Начинаем выдачу предмета ID={$item_id} x{$qty} для персонажа {$char_id}", 'deposit');

            // Ищем, есть ли уже такой предмет (Адена стакается, но проверяем)
            $existing = (new \yii\db\Query())
                ->from($gs->db_name . '.items')
                ->where(['owner_id' => $char_id, 'item_id' => $item_id, 'loc' => 'INVENTORY'])
                ->one($gameDb);

            if ($existing) {
                Yii::info("Предмет найден (object_id: {$existing['object_id']}, count: {$existing['count']}). Обновляем...", 'deposit');
                
                // Используем Expression, чтобы прибавить на стороне MySQL
                $gameDb->createCommand()->update(
                    $gs->db_name . '.items',
                    ['count' => new Expression('count + :qty', [':qty' => $qty])],
                    ['object_id' => (int)$existing['object_id']]
                )->execute();
                
            } else {
                Yii::info("Предмет не найден. Создаем новую запись...", 'deposit');
                
                // Получаем следующий object_id
                $maxId = (new \yii\db\Query())
                    ->from($gs->db_name . '.items')
                    ->max('object_id', $gameDb);
                $newObjId = (int)$maxId + 1;

                // Прямая вставка в таблицу items
                $gameDb->createCommand()->insert($gs->db_name . '.items', [
                    'object_id'     => $newObjId,
                    'owner_id'      => $char_id,
                    'item_id'       => $item_id,
                    'count'         => $qty,
                    'enchant_level' => 0,
                    'loc'           => 'INVENTORY',
                    'loc_data'      => 0,
                ])->execute();
            }

            Yii::info("Успешно выдан предмет ID={$item_id} x{$qty} персонажу {$char_id}", 'deposit');
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