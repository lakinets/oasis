<?php
namespace app\modules\cabinet\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\PaymentTransaction;

use app\components\payments\RobokassaGateway;
use app\components\payments\UnitpayGateway;
use app\components\payments\NOWPaymentsGateway;
use app\components\payments\PayOpGateway;
use app\components\payments\CryptomusGateway;
use app\components\payments\VoletGateway;
use app\components\payments\InterkassaGateway;  // ← добавлено
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
            Yii::$app->session->setFlash('error', 'Неверные данные.');
            return $this->redirect(['index']);
        }

        $userId = (int)Yii::$app->user->id;
        $login  = (new \yii\db\Query())
            ->from('users')
            ->select('login')
            ->where(['user_id' => $userId])
            ->scalar();
        if (!$login) {
            Yii::$app->session->setFlash('error', 'Не удалось определить логин пользователя.');
            return $this->redirect(['index']);
        }

        $tx   = PaymentTransaction::create($provider, $userId, $login, $amount, 'RUB');
        $desc = 'Пополнение баланса';
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

    public function actionInterkassaCallback()   // ← добавлено
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
            $tx = \app\models\PaymentTransaction::findOne(['order_id' => $result['orderId'], 'provider' => $provider]);
            if ($tx && $tx->status !== 'paid') {
                $db = Yii::$app->db;
                $t  = $db->beginTransaction();
                try {
                    $db->createCommand(
                        'UPDATE user_profiles SET balance = balance + :sum WHERE user_id = :uid',
                        [':sum' => $result['amount'], ':uid' => $tx->user_id]
                    )->execute();

                    $tx->status    = 'paid';
                    $tx->paid_at   = date('Y-m-d H:i:s');
                    $tx->provider_invoice = $result['externalId'] ?? null;
                    $tx->payload   = json_encode(Yii::$app->request->bodyParams ?: Yii::$app->request->get());
                    $tx->save(false);
                    $t->commit();
                } catch (\Throwable $e) {
                    $t->rollBack();
                    return $result['rawResponse'] ?? 'internal error';
                }
            }
        }
        return $result['rawResponse'] ?? 'OK';
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
            'interkassa'  => new InterkassaGateway(),   // ← добавлено
            default       => throw new \RuntimeException('Unknown provider'),
        };
    }
}