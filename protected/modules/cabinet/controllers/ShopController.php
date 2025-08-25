<?php
namespace app\modules\cabinet\controllers;

use app\models\AllItems;
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
    /* ---------- Главная: список категорий ---------- */
    public function actionIndex()
    {
        return $this->render('index', [
            'categories' => ShopCategories::find()
                ->where(['status' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all(),
        ]);
    }

    /* ---------- Страница категории ---------- */
    public function actionCategory(string $category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link, 'status' => 1]);
        if (!$category) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $gs_id   = (int)Yii::$app->request->get('gs_id');
        $char_id = (int)Yii::$app->request->get('char_id');

        // активные серверы
        $servers = Gs::find()->where(['status' => 1])->orderBy('id')->all();
        // баланс пользователя
        $balance = (new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();
        $balance = $balance !== false ? (float)$balance : 0.0;

        $characters = [];
        if ($gs_id) {
            [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
            $login = Yii::$app->user->identity->login;

            $characters = $driver->charactersQuery()
                ->where(['account_name' => $login])
                ->orderBy(['char_name' => SORT_ASC])
                ->all($gameDb);
        }

        // проверка char_id
        if ($char_id) {
            $ok = false;
            foreach ($characters as $c) {
                if ((int)$c['char_id'] === $char_id) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                $char_id = null;
            }
        }

        $packs = ShopItemsPacks::find()
            ->where(['category_id' => $category->id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('category', [
            'servers'    => $servers,
            'gs_id'      => $gs_id ?: null,
            'characters' => $characters,
            'char_id'    => $char_id ?: null,
            'category'   => $category,
            'packs'      => $packs,
            'balance'    => $balance,
        ]);
    }

    /* ---------- Покупка пака ---------- */
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
            throw new BadRequestHttpException('Не хватает данных.');
        }

        $pack = ShopItemsPacks::findOne(['id' => $pack_id, 'status' => 1]);
        if (!$pack) {
            throw new NotFoundHttpException('Пак не найден.');
        }

        $items = ShopItems::find()
            ->where(['pack_id' => $pack->id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
        if (!$items) {
            Yii::$app->session->setFlash('error', 'Пак пуст.');
            return $this->redirect($request->referrer ?: ['index']);
        }

        // расчёт стоимости
        $total = 0.0;
        foreach ($items as $it) {
            $cost = (float)$it->cost;
            $discount = (float)($it->discount ?? 0);
            $total += round($cost * (1 - max(0, min($discount, 100)) / 100), 2);
        }

        $balance = (new \yii\db\Query())
            ->from('user_profiles')
            ->where(['user_id' => Yii::$app->user->id])
            ->select('balance')
            ->scalar();
        $balance = $balance !== false ? (float)$balance : 0.0;

        if ($balance < $total) {
            Yii::$app->session->setFlash('error', 'Недостаточно средств.');
            return $this->redirect($request->referrer ?: ['index']);
        }

        // подключение к игровой БД и драйвер
        [$gameDb, $driver] = $this->getGameDbAndDriver($gs_id);
        $login = Yii::$app->user->identity->login;

        // проверка владельца персонажа
        $ownerOk = $driver->charactersQuery()
            ->where(['char_id' => $char_id, 'account_name' => $login])
            ->exists($gameDb);
        if (!$ownerOk) {
            Yii::$app->session->setFlash('error', 'Персонаж не ваш.');
            return $this->redirect($request->referrer ?: ['index']);
        }

        // транзакция
        $tx = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand(
                'UPDATE user_profiles SET balance = balance - :amt WHERE user_id = :uid'
            )->bindValues([':amt' => $total, ':uid' => Yii::$app->user->id])->execute();

            foreach ($items as $it) {
                $driver->insertItem(
                    $char_id,
                    (int)$it->item_id,
                    max(1, (int)$it->count),
                    max(0, (int)$it->enchant)
                );
            }

            $tx->commit();
            Yii::$app->session->setFlash('success', 'Покупка успешно завершена.');
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e->getMessage(), 'shop.buy');
            Yii::$app->session->setFlash('error', 'Ошибка покупки: ' . $e->getMessage());
        }

        return $this->redirect(['/cabinet/shop/category', 'category_link' => $pack->category->link, 'gs_id' => $gs_id, 'char_id' => $char_id]);
    }

    /* ---------- [УНИВЕРСАЛЬНО] Получить подключение и драйвер ---------- */
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

        /** @var \app\l2j\ $driver */
        $driver = new $className($gameDb, $gs->db_name);

        return [$gameDb, $driver];
    }
}