<?php
namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Response;

use app\modules\backend\models\Tickets;
use app\modules\backend\models\TicketsSearch;
use app\modules\backend\models\TicketsAnswers;
use app\modules\backend\models\TicketsCategories;
use app\modules\backend\models\Gs;

class TicketsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules'  => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    /* ---------- 1. Список тикетов ---------- */
    public function actionIndex()
    {
        $searchModel  = new TicketsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        $gsList       = Gs::find()->indexBy('id')->select(['id', 'name'])->column();
        $categories   = TicketsCategories::find()->indexBy('id')->select(['id', 'title'])->column();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'gsList'       => $gsList,
            'categories'   => $categories,
        ]);
    }

    /* ---------- 2. Редактирование тикета + ответы ---------- */
    public function actionEdit($id)
    {
        $ticket = $this->findTicket($id);

        // Снимаем флаг "новое сообщение для админа"
        if ($ticket->new_message_for_admin == 1) {
            $ticket->new_message_for_admin = 0;
            $ticket->save(false, ['new_message_for_admin', 'updated_at']);
        }

        // Ответы
        $answersProvider = new ActiveDataProvider([
            'query' => TicketsAnswers::find()
                ->where(['ticket_id' => $id])
                ->with('userInfo')
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);

        $answerModel = new TicketsAnswers(['ticket_id' => $id]);

        if ($answerModel->load(Yii::$app->request->post()) && $answerModel->save()) {
            // уведомляем пользователя
            $ticket->new_message_for_user = 1;
            $ticket->save(false, ['new_message_for_user', 'updated_at']);

            Yii::$app->session->setFlash('success', Yii::t('backend', 'Ответ добавлен.'));
            return $this->redirect(['edit', 'id' => $id]);
        }

        return $this->render('edit', [
            'ticket'          => $ticket,
            'answerModel'     => $answerModel,
            'answersProvider' => $answersProvider,
        ]);
    }

    /* ---------- 3. Категории ---------- */
    public function actionCategories()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TicketsCategories::find()->orderBy(['sort' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('categories', ['dataProvider' => $dataProvider]);
    }

    public function actionCategoryForm($category_id = null)
    {
        $model = $category_id ? $this->findCategory($category_id) : new TicketsCategories();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', $category_id
                ? Yii::t('backend', 'Изменения сохранены.')
                : Yii::t('backend', 'Категория добавлена.')
            );
            return $this->redirect(['categories']);
        }

        return $this->render('category-form', ['model' => $model]);
    }

    public function actionCategoryAllow($category_id)
    {
        $model = $this->findCategory($category_id);
        $model->status = $model->status ? 0 : 1;
        $model->save(false, ['status']);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Статус изменён.'));
        return $this->redirect(['categories']);
    }

    public function actionCategoryDel($category_id)
    {
        $model = $this->findCategory($category_id);
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('backend', 'Категория удалена.'));
        return $this->redirect(['categories']);
    }

    /* ---------- 4. Поиск моделей ---------- */
    private function findTicket($id)
    {
        if (($model = Tickets::find()->with(['category', 'user'])->where(['id' => $id])->one()) === null) {
            throw new NotFoundHttpException('Тикет не найден.');
        }
        return $model;
    }

    private function findCategory($id)
    {
        if (($model = TicketsCategories::findOne($id)) === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }
        return $model;
    }
}