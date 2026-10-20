<?php
namespace app\modules\cabinet\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\PaymentTransaction;
use app\models\Gs;
use yii\db\Connection;

use app\components\payments\RobokassaGateway;
use app\components\payments\UnitpayGateway;
use app\components\payments\NOWPaymentsGateway;
use app\components\payments\PayOpGateway;
use app\components\payments\CryptomusGateway;
use app\components\payments\VoletGateway;
use app\components\payments\InterkassaGateway;
use app\components\payments\PaymentsManager;

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

        $tx   = PaymentTransaction::create($provider, $userId, $login, $amount, 'RUB');
        $desc = Yii::t('views', 'Пополнение баланса');
        $url  = $this->getGateway($provider)
                     ->buildRedirectUrl($tx->order_id, $tx->login, $tx->amount, $tx->currency, $desc);

        return $this->redirect($url);
    }

    /* ----------------- CALLBACKS ----------------- */

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
                $db = Yii::$app->db;
                $t  = $db->beginTransaction();
                try {
                    // Проверяем, есть ли payload (покупка предметов)
                    $payload = $tx->payload ? json_decode($tx->payload, true) : null;
                    
                    if (is_array($payload) && isset($payload['type']) && $payload['type'] === 'topup') {
                        // Это покупка предметов для инвентаря
                        $success = $this->_deliverTopupItems($payload);
                        if (!$success) {
                            throw new \RuntimeException('Ошибка выдачи предметов. Сервер недоступен.');
                        }
                    } else {
                        // Обычное пополнение баланса
                        $db->createCommand(
                            'UPDATE user_profiles SET balance = balance + :sum WHERE user_id = :uid',
                            [':sum' => $result['amount'], ':uid' => $tx->user_id]
                        )->execute();
                    }

                    $tx->status    = 'paid';
                    $tx->paid_at   = date('Y-m-d H:i:s');
                    $tx->provider_invoice = $result['externalId'] ?? null;
                    // Сохраняем ответ от платежки, чтобы не перетереть наш payload
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

    /**
     * Выдача предметов в игровой инвентарь
     */
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
                'password' => $gs->db_pass,
                'charset'  => 'utf8',
            ]);
            
            $className = '\\app\\l2j\\' . ucfirst((string)$gs->version);
            if (!class_exists($className)) return false;
            $driver = new $className($gameDb, $gs->db_name);

            // Логика стака: проверяем, есть ли уже такой предмет у персонажа в инвентаре
            $existing = (new \yii\db\Query())
                ->from($gs->db_name . '.items')
                ->where(['owner_id' => $char_id, 'item_id' => $item_id, 'loc' => 'INVENTORY'])
                ->one($gameDb);

            if ($existing) {
                // Если есть - обновляем count
                $gameDb->createCommand()->update(
                    $gs->db_name . '.items',
                    ['count' => (int)$existing['count'] + $qty],
                    ['object_id' => (int)$existing['object_id']]
                )->execute();
            } else {
                // Если нет - создаем новую запись через метод драйвера
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