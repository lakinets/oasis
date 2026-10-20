<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\backend\models\Gs;
use app\modules\backend\models\GsSearch;
use app\modules\backend\models\ShopCategories;
use app\modules\backend\models\ShopItemsPacks;
use app\modules\backend\models\ShopItems;
use app\modules\backend\models\ShopTopupConfig;
use app\modules\backend\models\ShopTopupTier;

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
            parent::behaviors(), // защита admin из BackendController
            [
                'verbs' => [
                    'class'   => VerbFilter::class,
                    'actions' => [
                        'del'               => ['POST'],
                        'shop-category-del' => ['GET', 'POST'],
                        'shop-pack-del'     => ['GET', 'POST'],
                        'shop-item-del'     => ['GET', 'POST'],
                        'shop-mode-toggle'  => ['POST'],
                        'shop-topup'        => ['GET', 'POST'],
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
            Yii::$app->session->setFlash(
                'success',
                $gs_id ? Yii::t('backend', 'Изменения сохранены.') : Yii::t('backend', 'Сервер добавлен.')
            );
            return $this->redirect(['index']);
        }
        return $this->render('form', ['model' => $model, 'versions' => $versions]);
    }

    public function actionAllow($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false);
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['index']);
    }

    public function actionDel($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Сервер удалён.'));
        return $this->redirect(['index']);
    }

    /* ---------- Магазин ---------- */

    public function actionShop($gs_id)
    {
        $gs = $this->findModel($gs_id);
        $categories = ShopCategories::find()
            ->where(['gs_id' => $gs_id])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $topupConfig = ShopTopupConfig::findForServer((int)$gs_id);
        if (!$topupConfig) {
            $topupConfig = new ShopTopupConfig(['gs_id' => (int)$gs_id, 'status' => 1]);
        }

        return $this->render('shop/index', [
            'gs'          => $gs,
            'categories'  => $categories,
            'topupConfig' => $topupConfig,
            'mode'        => $gs->shop_mode ?: 'shop',
        ]);
    }

    /**
     * Переключение режима магазина (shop | topup).
     */
    public function actionShopModeToggle($gs_id)
    {
        $gs = $this->findModel($gs_id);
        $mode = (string)Yii::$app->request->post('mode', 'shop');
        if (!in_array($mode, ['shop', 'topup'], true)) {
            $mode = 'shop';
        }
        $gs->shop_mode = $mode;
        $gs->save(false, ['shop_mode']);
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Режим магазина изменён.'));
        return $this->redirect(['shop', 'gs_id' => $gs_id]);
    }

    /**
     * Конфигурация режима "Пополнение" + сохранение порогов бонусов.
     */
    public function actionShopTopup($gs_id)
    {
        $gs = $this->findModel($gs_id);
        $config = ShopTopupConfig::findForServer((int)$gs_id);
        if (!$config) {
            $config = new ShopTopupConfig(['gs_id' => (int)$gs_id, 'status' => 1]);
        }

        if (Yii::$app->request->isPost) {
            $config->load(Yii::$app->request->post());

            $tx = Yii::$app->db->beginTransaction();
            try {
                if (!$config->save()) {
                    throw new \RuntimeException(json_encode($config->getErrors(), JSON_UNESCAPED_UNICODE));
                }

                // Обновляем пороги: удаляем старые, пишем новые
                ShopTopupTier::deleteAll(['topup_id' => $config->id]);
                $tiersPost = (array)Yii::$app->request->post('tiers', []);
                $sort = 0;
                foreach ($tiersPost as $t) {
                    $minQty   = (int)($t['min_qty'] ?? 0);
                    $bonusQty = (int)($t['bonus_qty'] ?? 0);
                    if ($minQty <= 0 || $bonusQty <= 0) {
                        continue; // пропускаем пустые строки
                    }
                    $tier = new ShopTopupTier([
                        'topup_id'  => $config->id,
                        'min_qty'   => $minQty,
                        'max_qty'   => max(0, (int)($t['max_qty'] ?? 0)),
                        'bonus_qty' => $bonusQty,
                        'sort'      => $sort++,
                    ]);
                    $tier->save(false);
                }

                $tx->commit();
                Yii::$app->session->setFlash('success', Yii::t('backend', 'Настройки пополнения сохранены.'));
                return $this->redirect(['shop', 'gs_id' => $gs_id]);
            } catch (\Throwable $e) {
                $tx->rollBack();
                Yii::error($e->getMessage(), 'backend.topup');
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Ошибка сохранения: {err}', [
                    'err' => $e->getMessage(),
                ]));
            }
        }

        return $this->render('shop/topup-form', [
            'gs'     => $gs,
            'config' => $config,
        ]);
    }

    public function actionShopCategory($gs_id, $category_id)
    {
        $gs = $this->findModel($gs_id);
        $category = $this->findCategory($category_id, $gs_id);
        $packs = ShopItemsPacks::find()
            ->where(['category_id' => $category_id, 'status' => 1])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
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
                $category_id ? Yii::t('backend', 'Категория обновлена.') : Yii::t('backend', 'Категория добавлена.')
            );
            return $this->redirect(['shop', 'gs_id' => $gs_id]);
        }

        return $this->render('shop/category-form', [
            'model' => $model,
            'gs'    => $this->findModel($gs_id),
        ]);
    }

    public function actionShopCategoryDel($category_id)
    {
        $category = ShopCategories::findOne($category_id);
        if ($category) {
            $gs_id = $category->gs_id;
            $category->delete();
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Категория удалена.'));
            return $this->redirect(['shop', 'gs_id' => $gs_id]);
        }
        return $this->redirect(['index']);
    }

    public function actionShopPack($gs_id, $category_id, $pack_id)
    {
        $gs = $this->findModel($gs_id);
        $category = $this->findCategory($category_id, $gs_id);
        $pack = $this->findPack($pack_id, $category_id);
        $items = ShopItems::find()
            ->where(['pack_id' => $pack_id])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
        return $this->render('shop/pack', [
            'gs'       => $gs,
            'category' => $category,
            'pack'     => $pack,
            'items'    => $items,
        ]);
    }

    public function actionShopPackForm($gs_id, $category_id, $pack_id = null)
    {
        $model = $pack_id
            ? ShopItemsPacks::findOne($pack_id)
            : new ShopItemsPacks(['category_id' => $category_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $pack_id ? Yii::t('backend', 'Пак обновлён.') : Yii::t('backend', 'Пак добавлен.')
            );
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
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Пак удалён.'));
            return $this->redirect(['shop-category', 'gs_id' => $gs_id, 'category_id' => $category_id]);
        }
        return $this->redirect(['index']);
    }

    public function actionShopItemForm($gs_id, $category_id, $pack_id, $item_id = null)
    {
        $model = $item_id
            ? ShopItems::findOne($item_id)
            : new ShopItems(['pack_id' => $pack_id, 'status' => 1]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $item_id ? Yii::t('backend', 'Изменения сохранены.') : Yii::t('backend', 'Предмет добавлен.')
            );
            return $this->redirect([
                'shop-pack',
                'gs_id'       => $gs_id,
                'category_id' => $category_id,
                'pack_id'     => $pack_id,
            ]);
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
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
            return $this->redirect([
                'shop-pack',
                'gs_id'       => $item->pack->category->gs_id,
                'category_id' => $item->pack->category_id,
                'pack_id'     => $item->pack_id,
            ]);
        }
        return $this->redirect(['index']);
    }

    public function actionShopItemDel($item_id)
    {
        $item = ShopItems::findOne($item_id);
        if ($item) {
            $pack_id     = $item->pack_id;
            $category_id = $item->pack->category_id;
            $gs_id       = $item->pack->category->gs_id;
            $item->delete();
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Предмет удалён.'));
            return $this->redirect([
                'shop-pack',
                'gs_id'       => $gs_id,
                'category_id' => $category_id,
                'pack_id'     => $pack_id,
            ]);
        }
        return $this->redirect(['index']);
    }

    /* ---------- helpers ---------- */

    protected function findModel($gs_id): Gs
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

    private function getVersionList(): array
    {
        $path  = Yii::getAlias('@app/../protected/l2j');
        $files = glob($path . '/*.php');
        $versions = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $name = basename($file, '.php');
                // Исключаем фабрику из списка выбора
                if ($name === 'DriverFactory') {
                    continue;
                }
                $versions[$name] = $name;
            }
            ksort($versions);
        }
        return $versions;
    }
}