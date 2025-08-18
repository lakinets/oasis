<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\ShopCategories;

class ShopController extends CabinetBaseController
{
    public function actionIndex()
    {
        return $this->render('index', [
            'categories' => ShopCategories::find()
                ->where(['status' => 1])
                ->orderBy(['sort' => SORT_ASC])
                ->all(),
        ]);
    }

    public function actionCategory($category_link)
    {
        $category = ShopCategories::findOne(['link' => $category_link]);
        if (!$category) {
            throw new \yii\web\NotFoundHttpException('Категория не найдена.');
        }

        return $this->render('category', [
            'category' => $category,
        ]);
    }

    public function actionBuy($category_link)
    {
        \Yii::$app->session->setFlash('success', 'Покупка завершена');
        return $this->redirect(['category', 'category_link' => $category_link]);
    }
}