<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use app\modules\backend\models\Tickets;
use app\modules\backend\models\TicketsSearch;
use app\modules\backend\models\TicketsAnswers;
use app\modules\backend\models\TicketsCategories;
use app\modules\backend\models\Gs;

class TicketsController extends BackendController
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

    /************  TICKETS  ************/

    public function actionIndex()
    {
        $searchModel  = new TicketsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'gsList'       => Gs::find()->select(['name'])->indexBy('id')->column(),
            'categories'   => TicketsCategories::find()->select(['title'])->indexBy('id')->column(),
        ]);
    }

    public function actionCreate()
    {
        $model       = new Tickets();
        $answerModel = new TicketsAnswers();

        $categories = TicketsCategories::find()->select(['title'])->indexBy('id')->column();
        $servers    = Gs::find()->select(['name'])->indexBy('id')->column();

        if (
            Yii::$app->request->isPost &&
            $model->load(Yii::$app->request->post()) &&
            $answerModel->load(Yii::$app->request->post())
        ) {
            $model->user_id    = Yii::$app->user->id;
            $model->status     = Tickets::STATUS_OPEN;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');

            if ($model->save(false)) {
                $answerModel->ticket_id  = $model->id; // 🔑 связь с тикетом
                $answerModel->user_id    = Yii::$app->user->id;
                $answerModel->created_at = date('Y-m-d H:i:s');

                if ($answerModel->save(false)) {
                    Yii::$app->session->setFlash('success', Yii::t('backend', 'Тикет создан'));
                    return $this->redirect(['edit', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model'       => $model,
            'answerModel' => $answerModel,
            'categories'  => $categories,
            'servers'     => $servers,
        ]);
    }

    public function actionEdit($id)
    {
        $ticket = $this->findTicket($id);
        if ($ticket->new_message_for_admin == Tickets::STATUS_NEW_MESSAGE_ON) {
            $ticket->updateAttributes(['new_message_for_admin' => Tickets::STATUS_NEW_MESSAGE_OFF]);
        }

        $answersDataProvider = new ActiveDataProvider([
            'query' => TicketsAnswers::find()
                ->where(['ticket_id' => $id])
                ->with('userInfo')
                ->orderBy(['created_at' => SORT_ASC]),
            'pagination' => ['pageSize' => 10],
        ]);

        $answerModel = new TicketsAnswers(['ticket_id' => $id]);
        if (
            Yii::$app->request->isPost &&
            $answerModel->load(Yii::$app->request->post())
        ) {
            $answerModel->user_id    = Yii::$app->user->id;
            $answerModel->created_at = date('Y-m-d H:i:s');

            if ($answerModel->save(false)) {
                $ticket->updateAttributes(['new_message_for_user' => Tickets::STATUS_NEW_MESSAGE_ON]);
                Yii::$app->session->setFlash('success', Yii::t('backend', 'Ответ добавлен'));
                return $this->redirect(['edit', 'id' => $id]);
            }
        }

        return $this->render('edit', [
            'ticket'              => $ticket,
            'answerModel'         => $answerModel,
            'answersDataProvider' => $answersDataProvider,
        ]);
    }

    public function actionToggle($id)
    {
        $model = $this->findTicket($id);
        $model->updateAttributes([
            'status' => $model->status === Tickets::STATUS_OPEN
                ? Tickets::STATUS_CLOSED
                : Tickets::STATUS_OPEN
        ]);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён'));
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $this->findTicket($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Тикет удалён'));
        return $this->redirect(['index']);
    }

    /************  CATEGORIES  ************/

    public function actionCategories()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TicketsCategories::find()->orderBy(['sort' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('categories', ['dataProvider' => $dataProvider]);
    }

    public function actionCategoryForm($id = null)
    {
        $model = $id ? TicketsCategories::findOne($id) : new TicketsCategories();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Категория сохранена'));
            return $this->redirect(['categories']);
        }

        return $this->render('category-form', ['model' => $model]);
    }

    public function actionCategoryAllow($id)
    {
        $model = TicketsCategories::findOne($id);
        if ($model) {
            $model->status = $model->status == TicketsCategories::STATUS_ON
                ? TicketsCategories::STATUS_OFF
                : TicketsCategories::STATUS_ON;
            $model->save(false);
        }
        return $this->redirect(['categories']);
    }

    public function actionCategoryDel($id)
    {
        TicketsCategories::findOne($id)?->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Категория удалена'));
        return $this->redirect(['categories']);
    }

    /************  SERVICE  ************/

    private function findTicket($id)
    {
        $model = Tickets::find()->with(['category', 'user'])->where(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('backend', 'Тикет не найден'));
        }
        return $model;
    }
}
