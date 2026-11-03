<?php

namespace app\modules\cabinet\controllers;

use app\models\Gs;
use app\models\PaymentTransaction;
use app\models\ShopCategories;
use app\models\ShopItems;
use app\models\ShopItemsPacks;
use app\models\ShopTopupConfig; // Обновлено
use app\components\payments\PaymentsManager;
use app\components\payments\StripeGateway;
use app\components\payments\RobokassaGateway;
use app\components\payments\UnitpayGateway;
use app\components\payments\NOWPaymentsGateway;
use app\components\payments\PayOpGateway;
use app\components\payments\CryptomusGateway;
use app\components\payments\VoletGateway;
use app\components\payments\InterkassaGateway;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\db\Connection;
use yii\db\Expression;

class ShopController extends CabinetBaseController
{
    /* ---------- главная: категории ---------- */
    public function actionIndex()
    {
        $topupServer = Gs::find()->where(['status' => 1, 'shop_mode' => 'topup'])->one();
        if ($topupServer) {
            return $this->redirect(['topup', 'gs_id' => $topupServer->id]);
        }

        $balance = (float)(new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();

        return $this->render('index', [
            'categories'  => ShopCategories::find()
                ->where(['status' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all(),
            'balance'     => $balance,
        ]);
    }

    /* ==================== РЕЖИМ "ПОПОЛНЕНИЕ" ==================== */
    
    public function actionTopup()
    {
        $servers = Gs::find()->where(['status' => 1, 'shop_mode' => 'topup'])->orderBy('id')->all();
        if (empty($servers)) {
            throw new NotFoundHttpException(Yii::t('views', 'Режим пополнения отключен.'));
        }

        $gs_id   = (int)Yii::$app->request->get('gs_id', $servers[0]->id);
        $char_id = (int)Yii::$app->request->get('char_id');

        $gs = Gs::findOne(['id' => $gs_id, 'status' => 1, 'shop_mode' => 'topup']);
        if (!$gs) {
            throw new NotFoundHttpException(Yii::t('views', 'Сервер не найден или режим отключен.'));
        }

        $config = ShopTopupConfig::findForServer($gs_id);
        if (!$config || !$config->status) {
            throw new NotFoundHttpException(Yii::t('views', 'Конфигурация пополнения не настроена.'));
        }

        $tiers = $config->tiers;

        $characters = [];
        $ownerOk = false;

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $characters = $driver->charactersQuery()
            ->where(['account_name' => $login])
            ->orderBy(['char_name' => SORT_ASC])
            ->all($gameDb);

        if ($char_id) {
            foreach ($characters as $c) {
                if ((int)($c['char_id'] ?? $c['obj_Id'] ?? 0) === $char_id) {
                    $ownerOk = true;
                    break;
                }
            }
        }

        $providers = PaymentsManager::available();

        return $this->render('topup', [
            'servers'    => $servers,
            'gs'         => $gs,
            'gs_id'      => $gs_id,
            'char_id'    => $char_id,
            'characters' => $characters,
            'ownerOk'    => $ownerOk,
            'config'     => $config,
            'tiers'      => $tiers,
            'providers'  => $providers,
        ]);
    }

    public function actionTopupPay()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException(Yii::t('views', 'Неверный метод.'));
        }

        $gs_id    = (int)$request->post('gs_id');
        $char_id  = (int)$request->post('char_id');
        $qty      = (int)$request->post('qty');
        $provider = (string)$request->post('provider');

        if (!$gs_id || !$char_id || $qty <= 0 || !$provider) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Не все поля заполнены.'));
            return $this->redirect(['topup', 'gs_id' => $gs_id, 'char_id' => $char_id]);
        }

        $gs = Gs::findOne(['id' => $gs_id, 'status' => 1, 'shop_mode' => 'topup']);
        if (!$gs) {
            throw new NotFoundHttpException(Yii::t('views', 'Сервер не найден.'));
        }

        $config = ShopTopupConfig::findForServer($gs_id);
        if (!$config || !$config->status) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Конфигурация пополнения не настроена.'));
            return $this->redirect(['topup', 'gs_id' => $gs_id]);
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $ownerOk = false;
        $chars = $driver->charactersQuery()->where(['account_name' => $login])->all($gameDb);
        foreach ($chars as $c) {
            if ((int)($c['char_id'] ?? $c['obj_Id'] ?? 0) === $char_id) {
                $ownerOk = true;
                break;
            }
        }
        if (!$ownerOk) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Персонаж не ваш.'));
            return $this->redirect(['topup', 'gs_id' => $gs_id]);
        }

        $bonusQty = 0;
        foreach ($config->tiers as $tier) {
            if ($qty >= $tier->min_qty && ($tier->max_qty == 0 || $qty <= $tier->max_qty)) {
                $bonusQty = (int)$tier->bonus_qty;
                break;
            }
        }

        $totalQty = $qty + $bonusQty;
        $amount   = $qty * (float)$config->price;

        if ($amount <= 0) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Сумма к оплате должна быть больше нуля.'));
            return $this->redirect(['topup', 'gs_id' => $gs_id, 'char_id' => $char_id]);
        }

        $userId = (int)Yii::$app->user->id;

        $payload = [
            'type'     => 'topup',
            'gs_id'    => $gs_id,
            'char_id'  => $char_id,
            'item_id'  => (int)$config->item_id,
            'total_qty'=> $totalQty,
        ];

        $tx = PaymentTransaction::create($provider, $userId, $login, $amount, $config->currency ?: 'RUB', $payload);

        $desc = Yii::t('views', 'Пополнение баланса');
        $url  = $this->getGateway($provider)
                     ->buildRedirectUrl($tx->order_id, $tx->login, $tx->amount, $tx->currency, $desc);

        return $this->redirect($url);
    }

    /* ==================== СТАНДАРТНЫЙ РЕЖИМ ==================== */

    public function actionCategory(string $category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link, 'status' => 1]);
        if (!$category) {
            throw new NotFoundHttpException(Yii::t('views', 'Категория не найдена.'));
        }

        $gs_id = (int)Yii::$app->request->get('gs_id');

        $servers = Gs::find()->where(['status' => 1])->orderBy('id')->all();
        $balance = (float)(new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();

        $packs = [];
        if ($gs_id) {
            $packs = ShopItemsPacks::find()
                ->where(['category_id' => $category->id, 'status' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all();
        }

        return $this->render('category', [
            'servers'  => $servers,
            'gs_id'    => $gs_id ?: null,
            'category' => $category,
            'packs'    => $packs,
            'balance'  => $balance,
        ]);
    }

    public function actionPack(string $category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link, 'status' => 1]);
        if (!$category) {
            throw new NotFoundHttpException(Yii::t('views', 'Категория не найдена.'));
        }

        $pack_id = (int)Yii::$app->request->get('pack_id');
        $gs_id   = (int)Yii::$app->request->get('gs_id');
        $char_id = (int)Yii::$app->request->get('char_id');

        if (!$pack_id || !$gs_id) {
            throw new NotFoundHttpException(Yii::t('views', 'Не хватает данных.'));
        }

        $pack = ShopItemsPacks::findOne(['id' => $pack_id, 'category_id' => $category->id, 'status' => 1]);
        if (!$pack) {
            throw new NotFoundHttpException(Yii::t('views', 'Пак не найден.'));
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $characters = $driver->charactersQuery()
            ->where(['account_name' => $login])
            ->orderBy(['char_name' => SORT_ASC])
            ->all($gameDb);

        $ownerOk = false;
        foreach ($characters as $c) {
            if ((int)($c['char_id'] ?? $c['obj_Id'] ?? 0) === $char_id) {
                $ownerOk = true;
                break;
            }
        }

        $items = ShopItems::find()
            ->where(['pack_id' => $pack->id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $balance = (float)(new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();

        return $this->render('pack', [
            'category'   => $category,
            'pack'       => $pack,
            'items'      => $items,
            'gs_id'      => $gs_id,
            'char_id'    => $char_id,
            'characters' => $characters,
            'balance'    => $balance,
            'ownerOk'    => $ownerOk,
        ]);
    }

    public function actionBuyItem()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException(Yii::t('views', 'Неверный метод.'));
        }

        $item_id = (int)$request->post('item_id');
        $gs_id   = (int)$request->post('gs_id');
        $char_id = (int)$request->post('char_id');

        if (!$item_id || !$gs_id || !$char_id) {
            throw new BadRequestHttpException(Yii::t('views', 'Ошибка данных MySQL, данные персонажей недоступны.'));
        }

        $item = ShopItems::findOne(['id' => $item_id, 'status' => 1]);
        if (!$item) {
            throw new NotFoundHttpException(Yii::t('views', 'Предмет не найден.'));
        }

        $cost     = (float)$item->cost;
        $discount = max(0, min((float)($item->discount ?? 0), 100));
        $price    = round($cost * (1 - $discount / 100), 2);

        $balance = (float)(new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();

        if ($balance < $price) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Недостаточно средств.'));
            return $this->redirect($request->referrer ?: ['index']);
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $ownerOk = $driver->charactersQuery()
            ->where(['obj_Id' => $char_id, 'account_name' => $login])
            ->exists($gameDb);
        if (!$ownerOk) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Персонаж не ваш.'));
            return $this->redirect($request->referrer ?: ['index']);
        }

        $tx = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand(
                'UPDATE user_profiles SET balance = balance - :price WHERE user_id = :uid'
            )->bindValues([
                ':price' => $price,
                ':uid'   => Yii::$app->user->id,
            ])->execute();

            $itemId  = (int)$item->item_id;
            $qty     = max(1, (int)$item->count);
            $enchant = max(0, (int)$item->enchant);

            $existing = (new \yii\db\Query())
                ->from($gs->db_name . '.items')
                ->where(['owner_id' => $char_id, 'item_id' => $itemId, 'loc' => 'INVENTORY'])
                ->one($gameDb);

            if ($existing && $enchant === 0) {
                $gameDb->createCommand()->update(
                    $gs->db_name . '.items',
                    ['count' => new Expression('count + :qty', [':qty' => $qty])],
                    ['object_id' => (int)$existing['object_id']]
                )->execute();
            } else {
                $maxId = (new \yii\db\Query())->from($gs->db_name . '.items')->max('object_id', $gameDb);
                $gameDb->createCommand()->insert($gs->db_name . '.items', [
                    'object_id'     => (int)$maxId + 1,
                    'owner_id'      => $char_id,
                    'item_id'       => $itemId,
                    'count'         => $qty,
                    'enchant_level' => $enchant,
                    'loc'           => 'INVENTORY',
                    'loc_data'      => 0,
                ])->execute();
            }

            $tx->commit();
            Yii::$app->session->setFlash('success', Yii::t('views', 'Покупка завершена.'));
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e->getMessage(), 'shop.buy');
            Yii::$app->session->setFlash('error', Yii::t('views', 'Ошибка покупки: {error}', [
                'error' => $e->getMessage(),
            ]));
        }

        return $this->redirect([
            '/cabinet/shop/pack',
            'category_link' => $item->pack->category->link,
            'pack_id' => $item->pack_id,
            'gs_id'   => $gs_id,
            'char_id' => $char_id,
        ]);
    }

    public function actionBuy()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException(Yii::t('views', 'Неверный метод.'));
        }

        $pack_id = (int)$request->post('pack_id');
        $gs_id   = (int)$request->post('gs_id');
        $char_id = (int)$request->post('char_id');

        if (!$pack_id || !$gs_id || !$char_id) {
            throw new BadRequestHttpException(Yii::t('views', 'Ошибка данных MySQL, покупка предметов невозможна.'));
        }

        $pack = ShopItemsPacks::findOne(['id' => $pack_id, 'status' => 1]);
        if (!$pack) {
            throw new NotFoundHttpException(Yii::t('views', 'Пак не найден.'));
        }

        $items = ShopItems::find()
            ->where(['pack_id' => $pack_id, 'status' => 1])
            ->all();

        if (empty($items)) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Пак пуст.'));
            return $this->redirect($request->referrer ?: ['index']);
        }

        $total = 0;
        foreach ($items as $it) {
            $cost     = (float)$it->cost;
            $discount = max(0, min((float)($it->discount ?? 0), 100));
            $total   += round($cost * (1 - $discount / 100), 2);
        }

        $balance = (float)(new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();

        if ($balance < $total) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Недостаточно средств.'));
            return $this->redirect($request->referrer ?: ['index']);
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $ownerOk = $driver->charactersQuery()
            ->where(['obj_Id' => $char_id, 'account_name' => $login])
            ->exists($gameDb);
        if (!$ownerOk) {
            Yii::$app->session->setFlash('error', Yii::t('views', 'Персонаж не ваш.'));
            return $this->redirect($request->referrer ?: ['index']);
        }

        $tx = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand(
                'UPDATE user_profiles SET balance = balance - :total WHERE user_id = :uid'
            )->bindValues([
                ':total' => $total,
                ':uid'   => Yii::$app->user->id,
            ])->execute();

            foreach ($items as $it) {
                $itemId  = (int)$it->item_id;
                $qty     = max(1, (int)$it->count);
                $enchant = max(0, (int)$it->enchant);

                $existing = (new \yii\db\Query())
                    ->from($gs->db_name . '.items')
                    ->where(['owner_id' => $char_id, 'item_id' => $itemId, 'loc' => 'INVENTORY'])
                    ->one($gameDb);

                if ($existing && $enchant === 0) {
                    $gameDb->createCommand()->update(
                        $gs->db_name . '.items',
                        ['count' => new Expression('count + :qty', [':qty' => $qty])],
                        ['object_id' => (int)$existing['object_id']]
                    )->execute();
                } else {
                    $maxId = (new \yii\db\Query())->from($gs->db_name . '.items')->max('object_id', $gameDb);
                    $gameDb->createCommand()->insert($gs->db_name . '.items', [
                        'object_id'     => (int)$maxId + 1,
                        'owner_id'      => $char_id,
                        'item_id'       => $itemId,
                        'count'         => $qty,
                        'enchant_level' => $enchant,
                        'loc'           => 'INVENTORY',
                        'loc_data'      => 0,
                    ])->execute();
                }
            }

            $tx->commit();
            Yii::$app->session->setFlash('success', Yii::t('views', 'Пак куплен.'));
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e->getMessage(), 'shop.buy');
            Yii::$app->session->setFlash('error', Yii::t('views', 'Ошибка покупки: {error}', [
                'error' => $e->getMessage(),
            ]));
        }

        return $this->redirect([
            '/cabinet/shop/pack',
            'category_link' => $pack->category->link,
            'pack_id' => $pack->id,
            'gs_id'   => $gs_id,
            'char_id' => $char_id,
        ]);
    }

    /* ---------- helpers ---------- */
    
    private function getGameDbAndDriver(int $gs_id): array
    {
        $gs = Gs::findOne(['id' => $gs_id, 'status' => 1]);
        if (!$gs) {
            throw new NotFoundHttpException(Yii::t('views', 'Сервер не найден или выключен.'));
        }

        $dsn = "mysql:host={$gs->db_host};port={$gs->db_port};dbname={$gs->db_name}";
        $gameDb = new Connection([
            'dsn'      => $dsn,
            'username' => $gs->db_user,
            'password' => $gs->db_pass,
            'charset'  => 'utf8',
        ]);

        $className = '\\app\\l2j\\' . ucfirst((string)$gs->version);
        if (!class_exists($className)) {
            throw new NotFoundHttpException(Yii::t('views', 'Конфигурация "{version}" не найдена.', [
                'version' => $gs->version,
            ]));
        }

        $driver = new $className($gameDb, $gs->db_name);
        return [$gameDb, $driver];
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
            default       => throw new BadRequestHttpException('Неизвестная платёжная система: ' . $provider),
        };
    }
}