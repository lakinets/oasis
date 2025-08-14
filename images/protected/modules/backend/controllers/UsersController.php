<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

use app\modules\backend\models\Users;
use app\modules\backend\models\UsersSearch;
use app\modules\backend\models\UsersProfile;
use app\modules\backend\models\UserBonuses;
use app\modules\backend\models\UsersAuthLogs;
use app\modules\backend\models\Transactions;
use app\modules\backend\models\Referals;
use app\modules\backend\models\EditUserForm;
use app\modules\backend\models\UserMessages;
use app\modules\backend\models\PurchaseItemsLog;

class UsersController extends Controller
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

    /* ---------- Главный список ---------- */
    public function actionIndex()
    {
        $searchModel  = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /* ---------- Рефералы ---------- */
    public function actionReferals($user_id)
    {
        $user = $this->findModel($user_id);

        $dataProvider = new ActiveDataProvider([
            'query' => Referals::find()
                ->where(['referer' => $user_id])
                ->with(['referalInfo.profile']),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('referals', [
            'user'         => $user,
            'dataProvider' => $dataProvider,
        ]);
    }

    /* ---------- История авторизации ---------- */
    public function actionAuthHistory($user_id)
    {
        $searchModel  = new UsersAuthLogs();
        $searchModel->user_id = $user_id;
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('auth-history', [
            'user'         => $this->findModel($user_id),
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /* ---------- История транзакций ---------- */
    public function actionTransactionHistory($user_id)
    {
        $searchModel  = new Transactions();
        $searchModel->user_id = $user_id;
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('transaction-history', [
            'user'         => $this->findModel($user_id),
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'aggregators'  => [], // здесь можно передать список агрегаторов
        ]);
    }

    /* ---------- Карточка юзера ---------- */
    public function actionView($user_id)
    {
        $user = $this->findModel($user_id);

        return $this->render('view', [
            'user' => $user,
        ]);
    }

    /* ---------- Удалить бонус ---------- */
    public function actionDelBonus($user_id, $bonus_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = UserBonuses::findOne(['id' => $bonus_id, 'user_id' => $user_id]);
        if (!$model) {
            return ['status' => false, 'msg' => 'Бонус не найден'];
        }

        $model->delete();
        return ['status' => true, 'msg' => 'Бонус удалён'];
    }

    /* ---------- Добавить бонус ---------- */
    public function actionAddBonus($user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new UserBonuses(['user_id' => $user_id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['status' => true, 'msg' => 'Бонус добавлен'];
        }
        return ['status' => false, 'errors' => $model->getErrors()];
    }

    /* ---------- Отправить сообщение ---------- */
    public function actionAddMessage($user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new UserMessages(['user_id' => $user_id]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['status' => true, 'msg' => 'Сообщение отправлено'];
        }
        return ['status' => false, 'errors' => $model->getErrors()];
    }

    /* ---------- Сменить роль ---------- */
    public function actionChangeRole($user_id)
    {
        $user = $this->findModel($user_id);
        $user->role = $user->role == Users::ROLE_ADMIN ? Users::ROLE_DEFAULT : Users::ROLE_ADMIN;
        $user->auth_hash = null;
        $user->save(false);

        Yii::$app->session->setFlash('success', 'Роль изменена');
        return $this->redirectBack();
    }

    /* ---------- Редактировать данные ---------- */
    public function actionEditData($user_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = $this->findModel($user_id);
        $form = new EditUserForm($user);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            return ['status' => true, 'msg' => 'Данные сохранены'];
        }
        return ['status' => false, 'errors' => $form->getErrors()];
    }

    /* ---------- Лог покупок ---------- */
    public function actionItemPurchaseLog($user_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PurchaseItemsLog::find()
                ->where(['user_id' => $user_id])
                ->orderBy(['created_at' => SORT_DESC])
                ->with(['itemInfo', 'gs']),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('item-purchase-log', [
            'user'         => $this->findModel($user_id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /* ---------- Вспомогательные ---------- */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Юзер не найден.');
    }
}