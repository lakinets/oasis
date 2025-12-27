<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\backend\models\Gs;
use app\modules\backend\models\GsSearch;
use app\modules\backend\models\ShopCategories;
use app\modules\backend\models\ShopItemsPacks;
use app\modules\backend\models\ShopItems;

/**
 * Управление игровыми серверами и магазином.
 * Наследует BackendController => доступ только для admin.
 */
class GameServersController extends BackendController
{
    /**
     * Дополняем родительские поведения ограничением HTTP-методов
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(), // <-- подключаем защиту admin из BackendController
            [
                'verbs' => [
                    'class'   => VerbFilter::class,
                    'actions' => [
                        'del'               => ['POST'],
                        'shop-category-del' => ['POST'],
                        'shop-pack-del'     => ['POST'],
                        'shop-item-del'     => ['POST'],
                    ],
                ],
            ]
        );
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

    /* ---------- Сервер ---------- */
    public function actionForm($gs_id = null)
    {
        $model = $gs_id ? $this->findModel($gs_id) : new Gs();
        $versions = $this->getVersionList();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $gs_id ? 'Изменения сохранены.' : 'Сервер добавлен.');
            return $this->redirect(['index']);
        }
        return $this->render('form', ['model' => $model, 'versions' => $versions]);
    }

    public function actionAllow($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Статус изменён.');
        return $this->redirect(['index']);
    }

    public function actionDel($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Сервер удалён.');
        return $this->redirect(['index']);
    }

    /* ---------- Магазин ---------- */

    public function actionShop($gs_id)
    {
        $gs = $this->findModel($gs_id);
        $categories = ShopCategories::find()->where(['gs_id' => $gs_id])->orderBy(['sort' => SORT_ASC])->all();
        return $this->render('shop/index', ['gs' => $gs, 'categories' => $categories]);
    }

    public function actionShopCategory($gs_id, $category_id)
    {
        $gs = $this->findModel($gs_id);
        $category = $this->findCategory($category_id, $gs_id);
        $packs = ShopItemsPacks::find()->where(['category_id' => $category_id, 'status' => 1])->orderBy(['sort' => SORT_ASC])->all();
        return $this->render('shop/category', ['gs' => $gs, 'category' => $category, 'packs' => $packs]);
    }

    public function actionShopCategoryForm($gs_id, $category_id = null)
    {
        $model = $category_id
            ? ShopCategories::findOne(['id' => $category_id, 'gs_id' => $gs_id])
            : new ShopCategories(['gs_id' => $gs_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $category_id ? 'Категория обновлена.' : 'Категория добавлена.'
            );
            return $this->redirect(['shop', 'gs_id' => $gs_id]);
        }

        return $this->render('shop/category-form', [
            'model' => $model,
            'gs' => $this->findModel($gs_id),
        ]);
    }

    public function actionShopCategoryDel($category_id)
    {
        $category = ShopCategories::findOne($category_id);
        if ($category) {
            $gs_id = $category->gs_id;
            $category->delete();
            Yii::$app->session->setFlash('success', 'Категория удалена.');
        }
        return $this->redirect(['shop', 'gs_id' => $gs_id]);
    }

    public function actionShopPack($gs_id, $category_id, $pack_id)
    {
        $gs = $this->findModel($gs_id);
        $category = $this->findCategory($category_id, $gs_id);
        $pack = $this->findPack($pack_id, $category_id);
        $items = ShopItems::find()->where(['pack_id' => $pack_id])->orderBy(['sort' => SORT_ASC])->all();
        return $this->render('shop/pack', ['gs' => $gs, 'category' => $category, 'pack' => $pack, 'items' => $items]);
    }

    public function actionShopPackForm($gs_id, $category_id, $pack_id = null)
    {
        $model = $pack_id ? ShopItemsPacks::findOne($pack_id) : new ShopItemsPacks(['category_id' => $category_id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $pack_id ? 'Пак обновлён.' : 'Пак добавлен.');
            return $this->redirect(['shop-category', 'gs_id' => $gs_id, 'category_id' => $category_id]);
        }
        return $this->render('shop/pack-form', [
            'model'    => $model,
            'gs'       => $this->findModel($gs_id),
            'category' => $this->findCategory($category_id, $gs_id),
        ]);
    }

    public function actionShopPackDel($pack_id)
    {
        $pack = ShopItemsPacks::findOne($pack_id);
        if ($pack) {
            $category_id = $pack->category_id;
            $gs_id = $pack->category->gs_id;
            $pack->delete();
            Yii::$app->session->setFlash('success', 'Пак удалён.');
        }
        return $this->redirect(['shop-category', 'gs_id' => $gs_id, 'category_id' => $category_id]);
    }

    public function actionShopItemForm($gs_id, $category_id, $pack_id, $item_id = null)
    {
        $model = $item_id ? ShopItems::findOne($item_id) : new ShopItems(['pack_id' => $pack_id, 'status' => 1]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $item_id ? 'Изменения сохранены.' : 'Предмет добавлен.');
            return $this->redirect(['shop-pack', 'gs_id' => $gs_id, 'category_id' => $category_id, 'pack_id' => $pack_id]);
        }
        return $this->render('shop/item-form', [
            'model'    => $model,
            'gs'       => $this->findModel($gs_id),
            'category' => $this->findCategory($category_id, $gs_id),
            'pack'     => $this->findPack($pack_id, $category_id),
        ]);
    }

    public function actionShopItemToggle($item_id)
    {
        $item = ShopItems::findOne($item_id);
        if ($item) {
            $item->updateAttributes(['status' => 1 - $item->status]);
            Yii::$app->session->setFlash('success', 'Статус изменён.');
        }
        return $this->redirect(['shop-pack', 'gs_id' => $item->pack->category->gs_id, 'category_id' => $item->pack->category_id, 'pack_id' => $item->pack_id]);
    }

    public function actionShopItemDel($item_id)
    {
        $item = ShopItems::findOne($item_id);
        if ($item) {
            $pack_id = $item->pack_id;
            $category_id = $item->pack->category_id;
            $gs_id = $item->pack->category->gs_id;
            $item->delete();
            Yii::$app->session->setFlash('success', 'Предмет удалён.');
        }
        return $this->redirect(['shop-pack', 'gs_id' => $gs_id, 'category_id' => $category_id, 'pack_id' => $pack_id]);
    }

    /* ---------- helpers ---------- */

    protected function findModel($gs_id): Gs
    {
        if (($model = Gs::findOne($gs_id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Сервер не найден.');
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

    private function getVersionList(): array
    {
        $path = Yii::getAlias('@app/../protected/l2j');
        $files = glob($path . '/*.php');
        $versions = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $versions[basename($file, '.php')] = basename($file, '.php');
            }
            ksort($versions);
        }
        return $versions;
    }
}