<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\modules\backend\models\ShopCategories;
use app\modules\backend\models\ShopItemsPacks;
use app\modules\backend\models\ShopItems;
use app\modules\backend\models\Gs;
use app\components\GameServerInventory;

class ShopController extends Controller
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

    /* ---------- список категорий ---------- */
    public function actionIndex($gs_id)
    {
        $gs        = $this->findGs($gs_id);
        $categories = ShopCategories::find()
            ->where(['gs_id' => $gs_id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'gs'        => $gs,
            'categories'=> $categories,
        ]);
    }

    /* ---------- паки в категории ---------- */
    public function actionCategory($gs_id, $category_id)
    {
        $gs        = $this->findGs($gs_id);
        $category  = $this->findCategory($category_id, $gs_id);
        $packs     = ShopItemsPacks::find()
            ->where(['category_id' => $category_id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('category', [
            'gs'       => $gs,
            'category' => $category,
            'packs'    => $packs,
        ]);
    }

    /* ---------- предметы в паке ---------- */
    public function actionPack($gs_id, $category_id, $pack_id)
    {
        $gs        = $this->findGs($gs_id);
        $category  = $this->findCategory($category_id, $gs_id);
        $pack      = $this->findPack($pack_id, $category_id);
        $items     = ShopItems::find()
            ->where(['pack_id' => $pack_id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('pack', [
            'gs'       => $gs,
            'category' => $category,
            'pack'     => $pack,
            'items'    => $items,
        ]);
    }

    /* ---------- AJAX покупка ---------- */
    public function actionBuy()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $item   = ShopItems::findOne($post['item_id'] ?? 0);
        if (!$item) {
            return ['error' => 'Предмет не найден'];
        }

        // 1. Проверяем баланс пользователя (пример)
        $user = Yii::$app->user->identity;
        if ($user->balance < $item->cost) {
            return ['error' => 'Недостаточно средств'];
        }

        // 2. Снимаем деньги
        $user->updateCounters(['balance' => -$item->cost]);

        // 3. Отправляем предмет в инвентарь сервера
        $ok = GameServerInventory::addItem(
            $post['gs_id'] ?? 1,
            $post['player_id'] ?? 0,
            $item->item_id,
            $item->count
        );
        if (!$ok) {
            return ['error' => 'Ошибка при добавлении предмета'];
        }

        return ['success' => true];
    }

    /* ---------- helpers ---------- */
    private function findGs($id)
    {
        if (($model = Gs::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Сервер не найден');
    }

    private function findCategory($id, $gs_id)
    {
        if (($model = ShopCategories::findOne(['id' => $id, 'gs_id' => $gs_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Категория не найдена');
    }

    private function findPack($id, $category_id)
    {
        if (($model = ShopItemsPacks::findOne(['id' => $id, 'category_id' => $category_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Пак не найден');
    }
}