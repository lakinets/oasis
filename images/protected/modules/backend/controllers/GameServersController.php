<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

use app\modules\backend\models\Gs;
use app\modules\backend\models\ShopCategories;
use app\modules\backend\models\ShopItemsPacks;
use app\modules\backend\models\ShopItems;

class GameServersController extends Controller
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

    /* ------------------------------------------------------------------ */
    /* CRUD для серверов                                                  */
    /* ------------------------------------------------------------------ */

    public function actionIndex()
    {
        $searchModel = new Gs();
        $searchModel->load(Yii::$app->request->get());
        $dataProvider = $searchModel->search();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionForm($gs_id = null)
    {
        $model = $gs_id ? $this->findModel($gs_id) : new Gs();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $gs_id
                ? Yii::t('backend', 'Изменения сохранены.')
                : Yii::t('backend', 'Сервер добавлен.')
            );
            return $this->redirect(['index']);
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionAllow($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['index']);
    }

    public function actionDel($gs_id)
    {
        $model = $this->findModel($gs_id);
        $model->status = $model::STATUS_DELETED;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Сервер <b>:name</b> удалён', [
            ':name' => Html::encode($model->name)
        ]));
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Gs::find()->andWhere(['id' => $id])->one()) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Сервер не найден.');
    }

    /* ------------------------------------------------------------------ */
    /* Категории магазина                                                 */
    /* ------------------------------------------------------------------ */

    public function actionShop($gs_id)
    {
        $gs = $this->findModel($gs_id);
        $categories = ShopCategories::find()
            ->where(['gs_id' => $gs_id])
            ->with('countPacks')
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('shop/category/index', [
            'gs'         => $gs,
            'categories' => $categories,
        ]);
    }

    public function actionShopCategoryForm($gs_id, $category_id = null)
    {
        $gs    = $this->findModel($gs_id);
        $model = $category_id ? $this->findCategory($category_id) : new ShopCategories(['gs_id' => $gs_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $category_id
                ? Yii::t('backend', 'Изменения сохранены.')
                : Yii::t('backend', 'Категория создана.')
            );
            return $this->redirect(['shop', 'gs_id' => $gs_id]);
        }

        return $this->render('shop/category/form', [
            'gs'    => $gs,
            'model' => $model,
        ]);
    }

    public function actionShopCategoryAllow($gs_id, $category_id)
    {
        $model = $this->findCategory($category_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['shop', 'gs_id' => $gs_id]);
    }

    public function actionShopCategoryDel($gs_id, $category_id)
    {
        $model = $this->findCategory($category_id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Категория и всё содержимое удалены.'));
        } else {
            Yii::$app->session->setFlash('error', $model->getFirstErrors());
        }
        return $this->redirect(['shop', 'gs_id' => $gs_id]);
    }

    /* ------------------------------------------------------------------ */
    /* Наборы                                                             */
    /* ------------------------------------------------------------------ */

    public function actionShopCategoryPacks($gs_id, $category_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ShopItemsPacks::find()
                ->where(['category_id' => $category_id])
                ->with('countItems')
                ->orderBy(['sort' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('shop/category/packs/index', [
            'gs'           => $this->findModel($gs_id),
            'category'     => $this->findCategory($category_id),
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionShopCategoryPacksForm($gs_id, $category_id, $pack_id = null)
    {
        $model = $pack_id
            ? $this->findPack($pack_id)
            : new ShopItemsPacks(['category_id' => $category_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $pack_id
                ? Yii::t('backend', 'Изменения сохранены.')
                : Yii::t('backend', 'Набор создан.')
            );
            return $this->redirect(['shop-category-packs', 'gs_id' => $gs_id, 'category_id' => $category_id]);
        }

        return $this->render('shop/category/packs/form', [
            'gs'       => $this->findModel($gs_id),
            'category' => $this->findCategory($category_id),
            'model'    => $model,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Предметы в наборе                                                  */
    /* ------------------------------------------------------------------ */

    public function actionShopCategoryPackItems($gs_id, $category_id, $pack_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ShopItems::find()
                ->where(['pack_id' => $pack_id])
                ->with('itemInfo')
                ->orderBy(['sort' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('shop/category/packs/items/index', [
            'gs'           => $this->findModel($gs_id),
            'category'     => $this->findCategory($category_id),
            'pack'         => $this->findPack($pack_id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Вспомогательные методы                                             */
    /* ------------------------------------------------------------------ */

    protected function findCategory($id)
    {
        if (($model = ShopCategories::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Категория не найдена.');
    }

    protected function findPack($id)
    {
        if (($model = ShopItemsPacks::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Набор не найден.');
    }
}