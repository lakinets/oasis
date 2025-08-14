<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\Tickets;
use app\modules\backend\models\TicketsSearch;
use app\modules\backend\models\TicketsAnswers;
use app\modules\backend\models\TicketsCategories;
use app\modules\backend\models\Gs;

class TicketsController extends Controller
{
    /** {@inheritdoc} */
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

    /* ---------- 1. Список тикетов ---------- */
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

    /* ---------- 2. Создание тикета ---------- */
    public function actionCreate()
    {
        $model       = new Tickets();
        $answerModel = new TicketsAnswers();

        $categories = TicketsCategories::find()->select(['title'])->indexBy('id')->column();
        $servers    = Gs::find()->select(['name'])->indexBy('id')->column();

        if (
            Yii::$app->request->isPost &&
            $model->load(Yii::$app->request->post()) &&
            $answerModel->load(Yii::$app->request->post()) &&
            $model->validate() && $answerModel->validate()
        ) {
            $model->user_id = Yii::$app->user->id;
            $model->save(false);

            $answerModel->ticket_id = $model->id;
            $answerModel->user_id   = Yii::$app->user->id;
            $answerModel->save(false);

            Yii::$app->session->setFlash('success', Yii::t('backend', 'Тикет создан'));
            return $this->redirect(['edit', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model'       => $model,
            'answerModel' => $answerModel,
            'categories'  => $categories,
            'servers'     => $servers,
        ]);
    }

    /* ---------- 3. Просмотр / редактирование тикета + ответы ---------- */
    public function actionEdit($id)
    {
        $ticket = $this->findTicket($id);

        // Сбросить флаг "новое сообщение для админа"
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

        if (Yii::$app->request->isPost && $answerModel->load(Yii::$app->request->post()) && $answerModel->save()) {
            $ticket->updateAttributes(['new_message_for_user' => Tickets::STATUS_NEW_MESSAGE_ON]);
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Ответ добавлен'));
            return $this->redirect(['edit', 'id' => $id]);
        }

        return $this->render('edit', [
            'ticket'              => $ticket,
            'answerModel'         => $answerModel,
            'answersDataProvider' => $answersDataProvider,
        ]);
    }

    /* ---------- 4. Управление категориями ---------- */
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
        $model = $id ? $this->findCategory($id) : new TicketsCategories();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                $id ? Yii::t('backend', 'Изменения сохранены') : Yii::t('backend', 'Категория добавлена')
            );
            return $this->redirect(['categories']);
        }

        return $this->render('category-form', ['model' => $model]);
    }

    public function actionCategoryAllow($id)
    {
        $model = $this->findCategory($id);
        $model->updateAttributes(['status' => $model->status ? 0 : 1]);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён'));
        return $this->redirect(['categories']);
    }

    public function actionCategoryDel($id)
    {
        $this->findCategory($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Категория удалена'));
        return $this->redirect(['categories']);
    }

    /* ---------- 5. Служебные методы ---------- */
    private function findTicket($id)
    {
        $model = Tickets::find()->with(['category', 'user'])->where(['id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('backend', 'Тикет не найден'));
        }
        return $model;
    }

    private function findCategory($id)
    {
        $model = TicketsCategories::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('backend', 'Категория не найдена'));
        }
        return $model;
    }
}
