<?php

namespace app\modules\cabinet\controllers;

use app\models\Gs;
use app\modules\cabinet\models\ShopCategories;
use app\modules\cabinet\models\ShopItems;
use app\modules\cabinet\models\ShopItemsPacks;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\db\Connection;

class ShopController extends CabinetBaseController
{
    /* ---------- главная: категории ---------- */
    public function actionIndex()
    {
        $balance = (float)(new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();

        return $this->render('index', [
            'categories' => ShopCategories::find()
                ->where(['status' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all(),
            'balance'    => $balance,
        ]);
    }

    /* ---------- категория → паки ---------- */
    public function actionCategory(string $category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link, 'status' => 1]);
        if (!$category) {
            throw new NotFoundHttpException('Категория не найдена.');
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

    /* ---------- пак → товары ---------- */
    public function actionPack(string $category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link, 'status' => 1]);
        if (!$category) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $pack_id = (int)Yii::$app->request->get('pack_id');
        $gs_id   = (int)Yii::$app->request->get('gs_id');
        $char_id = (int)Yii::$app->request->get('char_id');

        if (!$pack_id || !$gs_id) {
            throw new NotFoundHttpException('Не хватает данных.');
        }

        $pack = ShopItemsPacks::findOne(['id' => $pack_id, 'category_id' => $category->id, 'status' => 1]);
        if (!$pack) {
            throw new NotFoundHttpException('Пак не найден.');
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        // Загружаем персонажей для селектора
        $characters = $driver->charactersQuery()
            ->where(['account_name' => $login])
            ->orderBy(['char_name' => SORT_ASC])
            ->all($gameDb);

        // Проверка владения персонажем
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

    /* ---------- покупка одного товара ---------- */
    public function actionBuyItem()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException('Неверный метод.');
        }

        $item_id = (int)$request->post('item_id');
        $gs_id   = (int)$request->post('gs_id');
        $char_id = (int)$request->post('char_id');

        if (!$item_id || !$gs_id || !$char_id) {
            throw new BadRequestHttpException('Ошибка данных MySQL, данные персонажей недоступны.');
        }

        $item = ShopItems::findOne(['id' => $item_id, 'status' => 1]);
        if (!$item) {
            throw new NotFoundHttpException('Предмет не найден.');
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
            Yii::$app->session->setFlash('error', 'Недостаточно средств.');
            return $this->redirect($request->referrer ?: ['index']);
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $ownerOk = $driver->charactersQuery()
            ->where(['obj_Id' => $char_id, 'account_name' => $login])
            ->exists($gameDb);
        if (!$ownerOk) {
            Yii::$app->session->setFlash('error', 'Персонаж не ваш.');
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

            $driver->insertItem(
                $char_id,
                (int)$item->item_id,
                max(1, (int)$item->count),
                max(0, (int)$item->enchant)
            );

            $tx->commit();
            Yii::$app->session->setFlash('success', 'Покупка завершена.');
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e->getMessage(), 'shop.buy');
            Yii::$app->session->setFlash('error', 'Ошибка покупки: ' . $e->getMessage());
        }

        return $this->redirect([
            '/cabinet/shop/pack',
            'category_link' => $item->pack->category->link,
            'pack_id' => $item->pack_id,
            'gs_id'   => $gs_id,
            'char_id' => $char_id,
        ]);
    }

    /* ---------- покупка пака ---------- */
    public function actionBuy()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            throw new BadRequestHttpException('Неверный метод.');
        }

        $pack_id = (int)$request->post('pack_id');
        $gs_id   = (int)$request->post('gs_id');
        $char_id = (int)$request->post('char_id');

        if (!$pack_id || !$gs_id || !$char_id) {
            throw new BadRequestHttpException('Ошибка данных MySQL, покупка предметов невозможна.');
        }

        $pack = ShopItemsPacks::findOne(['id' => $pack_id, 'status' => 1]);
        if (!$pack) {
            throw new NotFoundHttpException('Пак не найден.');
        }

        $items = ShopItems::find()
            ->where(['pack_id' => $pack_id, 'status' => 1])
            ->all();

        if (empty($items)) {
            Yii::$app->session->setFlash('error', 'Пак пуст.');
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
            Yii::$app->session->setFlash('error', 'Недостаточно средств.');
            return $this->redirect($request->referrer ?: ['index']);
        }

        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        $ownerOk = $driver->charactersQuery()
            ->where(['obj_Id' => $char_id, 'account_name' => $login])
            ->exists($gameDb);
        if (!$ownerOk) {
            Yii::$app->session->setFlash('error', 'Персонаж не ваш.');
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
                $driver->insertItem(
                    $char_id,
                    (int)$it->item_id,
                    max(1, (int)$it->count),
                    max(0, (int)$it->enchant)
                );
            }

            $tx->commit();
            Yii::$app->session->setFlash('success', 'Пак куплен.');
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e->getMessage(), 'shop.buy');
            Yii::$app->session->setFlash('error', 'Ошибка покупки: ' . $e->getMessage());
        }

        return $this->redirect([
            '/cabinet/shop/pack',
            'category_link' => $pack->category->link,
            'pack_id' => $pack->id,
            'gs_id'   => $gs_id,
            'char_id' => $char_id,
        ]);
    }

    /* ---------- универсальный метод подключения ---------- */
    private function getGameDbAndDriver(int $gs_id): array
    {
        $gs = Gs::findOne(['id' => $gs_id, 'status' => 1]);
        if (!$gs) {
            throw new NotFoundHttpException('Сервер не найден или выключен.');
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
            throw new NotFoundHttpException("Конфигурация \"{$gs->version}\" не найдена.");
        }

        $driver = new $className($gameDb, $gs->db_name);
        return [$gameDb, $driver];
    }
}