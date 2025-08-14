<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use app\modules\backend\models\Transactions;
use app\modules\backend\models\TransactionsSearch;

class TransactionsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    /**
     * Список транзакций (опционально по пользователю)
     * URL: /backend/transactions?user_id=123
     */
    public function actionIndex($user_id = null)
    {
        $searchModel  = new TransactionsSearch();
        $searchModel->user_id = $user_id;
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider'    => $dataProvider,
            'searchModel'     => $searchModel,
            'aggregatorsList' => [], // здесь можно передать список агрегаторов
        ]);
    }
}