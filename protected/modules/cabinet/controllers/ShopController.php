<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\ShopCategories;
use app\modules\cabinet\models\ShopItemsPacks;
use yii\data\ActiveDataProvider;

class ShopController extends CabinetBaseController
{
    /**
     * Главная страница магазина
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'categories' => ShopCategories::find()
                ->where(['status' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all(),
        ]);
    }

    /**
     * Страница категории
     */
    public function actionCategory($category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link]);
        if (!$category) {
            throw new \yii\web\NotFoundHttpException('Категория не найдена.');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => ShopItemsPacks::find()
                ->where(['category_id' => $category->id, 'status' => 1])
                ->orderBy(['sort' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('category', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Покупка пакета
     * URL: /cabinet/shop/buy?pack_id=123
     */
    public function actionBuy($pack_id)
    {
        $pack = ShopItemsPacks::findOne($pack_id);
        if (!$pack) {
            throw new \yii\web\NotFoundHttpException('Пакет не найден.');
        }

        $category = ShopCategories::findOne($pack->category_id);
        if (!$category) {
            throw new \yii\web\NotFoundHttpException('Категория не найдена.');
        }

        // TODO: реализовать логику покупки
        \Yii::$app->session->setFlash('success', 'Покупка завершена.');
        return $this->redirect(['category', 'category_link' => $category->link]);
    }
}