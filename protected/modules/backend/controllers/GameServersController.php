<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

use app\modules\backend\models\Gs;
use app\modules\backend\models\GsSearch;
use app\modules\backend\models\ShopCategories;
use app\modules\backend\models\ShopItemsPacks;
use app\modules\backend\models\ShopItems;

use app\components\GameServerInventory;

class GameServersController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    /* ---------- Главная ---------- */
    public function actionIndex()
    {
        $searchModel  = new GsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /* ---------- Создание / редактирование ---------- */
    public function actionForm($gs_id = null)
    {
        if ($gs_id !== null) {
            $model = $this->findModel($gs_id);
        } else {
            $model = new Gs();
        }

        $versionsDir = Yii::getAlias('@app/../protected/modules/backend/components/versions');
        $versions = [];
        if (is_dir($versionsDir)) {
            foreach (scandir($versionsDir) as $file) {
                if (is_file($versionsDir . DIRECTORY_SEPARATOR . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $versions[pathinfo($file, PATHINFO_FILENAME)] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
            ksort($versions);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $gs_id
                    ? Yii::t('backend', 'Изменения сохранены.')
                    : Yii::t('backend', 'Сервер добавлен.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('form', [
            'model'    => $model,
            'versions' => $versions,
        ]);
    }

    /* ---------- Переключение статуса ---------- */
    public function actionAllow($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false);
        return $this->redirect(['index']);
    }

    /* ---------- Удаление ---------- */
    public function actionDel($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Сервер удалён.'));
        return $this->redirect(['index']);
    }

    /* ---------- Магазин: категории ---------- */
    public function actionShop($gs_id)
    {
        $gs         = $this->findModel($gs_id);
        $categories = ShopCategories::find()
            ->where(['gs_id' => $gs_id])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('shop/index', [
            'gs'         => $gs,
            'categories' => $categories,
        ]);
    }

    /* ---------- Магазин: паки в категории ---------- */
    public function actionShopCategory($gs_id, $category_id)
    {
        $gs        = $this->findModel($gs_id);
        $category  = $this->findCategory($category_id, $gs_id);
        $packs     = ShopItemsPacks::find()
            ->where(['category_id' => $category_id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('shop/category', [
            'gs'       => $gs,
            'category' => $category,
            'packs'    => $packs,
        ]);
    }

    /* ---------- Магазин: предметы в паке ---------- */
    public function actionShopPack($gs_id, $category_id, $pack_id)
    {
        $gs        = $this->findModel($gs_id);
        $category  = $this->findCategory($category_id, $gs_id);
        $pack      = $this->findPack($pack_id, $category_id);
        $items     = ShopItems::find()
            ->where(['pack_id' => $pack_id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('shop/pack', [
            'gs'       => $gs,
            'category' => $category,
            'pack'     => $pack,
            'items'    => $items,
        ]);
    }

    /* ---------- AJAX покупка ---------- */
    public function actionBuy()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $item = ShopItems::findOne($post['item_id'] ?? 0);
        if (!$item) {
            return ['error' => 'Предмет не найден'];
        }

        $user = Yii::$app->user->identity;
        if ($user->balance < $item->cost) {
            return ['error' => 'Недостаточно средств'];
        }

        $ok = GameServerInventory::addItem(
            $post['gs_id'] ?? 1,
            $post['player_id'] ?? 0,
            $item->item_id,
            $item->count
        );
        if (!$ok) {
            return ['error' => 'Ошибка при добавлении предмета'];
        }

        $user->updateCounters(['balance' => -$item->cost]);
        return ['success' => true];
    }

    /* ---------- helpers ---------- */
    protected function findModel($gs_id)
    {
        if (($model = Gs::findOne($gs_id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('backend', 'Сервер не найден.'));
    }

    private function findCategory($id, $gs_id)
    {
        if (($model = ShopCategories::findOne(['id' => $id, 'gs_id' => $gs_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('backend', 'Категория не найдена'));
    }

    private function findPack($id, $category_id)
    {
        if (($model = ShopItemsPacks::findOne(['id' => $id, 'category_id' => $category_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('backend', 'Пак не найден'));
    }
}