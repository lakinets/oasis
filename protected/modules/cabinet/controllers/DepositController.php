<?php
namespace app\modules\cabinet\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\PaymentTransaction;
use app\components\payments\RobokassaGateway;
use app\components\payments\UnitpayGateway;
// use app\components\payments\WayToPayGateway; // удалено
use app\components\payments\NOWPaymentsGateway;
use app\components\payments\PayOpGateway;
use app\components\payments\CryptomusGateway;
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
        // список доступных провайдеров (название => label)
        $providers = PaymentsManager::available();
        return $this->render('index', ['providers' => $providers]);
    }

    public function actionCreate()
    {
        $req = Yii::$app->request;
        $amount   = (float)$req->post('amount', 0);
        $provider = (string)$req->post('provider', '');

        $allowed = array_keys(PaymentsManager::available()); // динамически
        if ($amount <= 0 || !in_array($provider, $allowed, true)) {
            Yii::$app->session->setFlash('error', 'Неверные данные.');
            return $this->redirect(['index']);
        }

        $userId = (int)Yii::$app->user->id;
        $login = (new \yii\db\Query())->from('users')->select('login')->where(['user_id' => $userId])->scalar();
        if (!$login) {
            Yii::$app->session->setFlash('error', 'Не удалось определить логин пользователя.');
            return $this->redirect(['index']);
        }

        $tx = PaymentTransaction::create($provider, $userId, (string)$login, $amount, 'RUB');

        $desc = 'Пополнение баланса';
        $gateway = $this->getGateway($provider);
        $redirectUrl = $gateway->buildRedirectUrl($tx->order_id, $tx->login, $tx->amount, $tx->currency, $desc);

        return $this->redirect($redirectUrl);
    }

    // ======= CALLBACKS =======

    public function actionRobokassaCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $gateway = $this->getGateway('robokassa');
        $result  = $gateway->handleCallback(Yii::$app->request);
        return $this->finalizeAndRespond($result, 'robokassa');
    }

    public function actionUnitpayCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $gateway = $this->getGateway('unitpay');
        $result  = $gateway->handleCallback(Yii::$app->request);
        return $this->finalizeAndRespond($result, 'unitpay');
    }

    // NOWPayments IPN
    public function actionNowpaymentsCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $gateway = $this->getGateway('nowpayments');
        $result  = $gateway->handleCallback(Yii::$app->request);
        return $this->finalizeAndRespond($result, 'nowpayments');
    }

    // PayOp (каркас)
    public function actionPayopCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $gateway = $this->getGateway('payop');
        $result  = $gateway->handleCallback(Yii::$app->request);
        return $this->finalizeAndRespond($result, 'payop');
    }

    // Cryptomus (каркас)
    public function actionCryptomusCallback()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $gateway = $this->getGateway('cryptomus');
        $result  = $gateway->handleCallback(Yii::$app->request);
        return $this->finalizeAndRespond($result, 'cryptomus');
    }

    private function finalizeAndRespond(array $result, string $provider)
    {
        if (!empty($result['orderId']) && !empty($result['ok'])) {
            $tx = PaymentTransaction::findOne(['order_id' => $result['orderId'], 'provider' => $provider]);
            if ($tx) {
                if ($tx->status !== 'paid' && isset($result['amount']) && $result['amount'] > 0) {
                    $db = Yii::$app->db;
                    $transaction = $db->beginTransaction();
                    try {
                        $db->createCommand(
                            "UPDATE user_profiles SET balance = balance + :sum WHERE user_id = :uid",
                            [':sum' => $result['amount'], ':uid' => $tx->user_id]
                        )->execute();

                        $tx->status = 'paid';
                        $tx->paid_at = date('Y-m-d H:i:s');
                        if (!empty($result['externalId'])) {
                            $tx->provider_invoice = (string)$result['externalId'];
                        }
                        $tx->payload = json_encode(Yii::$app->request->bodyParams ?: Yii::$app->request->get());
                        $tx->save(false);

                        $transaction->commit();
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        return $result['rawResponse'] ?? 'internal error';
                    }
                }
            }
        }
        return $result['rawResponse'] ?? 'OK';
    }

    public function actionSuccess(string $provider = null, string $orderId = null)
    {
        return $this->render('success', ['provider' => $provider, 'orderId' => $orderId]);
    }

    public function actionFail(string $provider = null, string $orderId = null, string $msg = null)
    {
        return $this->render('fail', ['provider' => $provider, 'orderId' => $orderId, 'msg' => $msg]);
    }

    private function getGateway(string $provider)
    {
        return match ($provider) {
            'robokassa'   => new RobokassaGateway(),
            'unitpay'     => new UnitpayGateway(),
            'nowpayments' => new NOWPaymentsGateway(),
            'payop'       => new PayOpGateway(),       // пока не активен в UI
            'cryptomus'   => new CryptomusGateway(),   // пока не активен в UI
            default       => throw new \RuntimeException('Unknown provider'),
        };
    }
}
