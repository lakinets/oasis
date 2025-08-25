<?php
namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\Tickets;
use app\modules\cabinet\models\TicketsForm;
use app\modules\cabinet\models\TicketsAnswers;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use Yii;

class TicketsController extends CabinetBaseController
{
    /* ---------- Список тикетов пользователя ---------- */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Tickets::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->with(['category'])
                ->orderBy(['status' => SORT_DESC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => Yii::$app->params['cabinet.tickets.limit'] ?? 10,
            ],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /* ---------- Создание тикета ---------- */
    public function actionAdd()
    {
        $model = new TicketsForm();

        $servers = ArrayHelper::map(
            (new \yii\db\Query())
                ->select(['id', 'name'])
                ->from('gs')
                ->all(),
            'id',
            'name'
        );

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $ticket = new Tickets([
                'user_id'               => Yii::$app->user->id,
                'category_id'           => $model->category_id,
                'priority'              => $model->priority,
                'title'                 => $model->title,
                'char_name'             => $model->char_name,
                'date_incident'         => $model->date_incident,
                'gs_id'                 => $model->gs_id,
                'status'                => Tickets::STATUS_OPEN,
                'new_message_for_user'  => 0,
                'new_message_for_admin' => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

            if ($ticket->save(false)) {
                $answer = new TicketsAnswers([
                    'ticket_id'  => $ticket->id,
                    'user_id'    => Yii::$app->user->id,
                    'text'       => $model->text,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $answer->save(false);

                Yii::$app->session->setFlash('success', 'Тикет успешно создан');
                return $this->redirect(['view', 'ticket_id' => $ticket->id]);
            }
        }

        return $this->render('add', [
            'model'   => $model,
            'servers' => $servers,
        ]);
    }

    /* ---------- Просмотр тикета ---------- */
    public function actionView($ticket_id)
    {
        $ticket = Tickets::findOne(['id' => $ticket_id, 'user_id' => Yii::$app->user->id]);
        if (!$ticket) {
            throw new NotFoundHttpException();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => TicketsAnswers::find()
                ->where(['ticket_id' => $ticket->id])
                ->orderBy(['created_at' => SORT_ASC]),
            'pagination' => [
                'pageSize' => Yii::$app->params['cabinet.tickets.answers.limit'] ?? 5,
            ],
        ]);

        $answerModel = new TicketsAnswers();

        if ($answerModel->load(Yii::$app->request->post()) && $answerModel->validate()) {
            $answerModel->ticket_id  = $ticket->id;
            $answerModel->user_id    = Yii::$app->user->id;
            $answerModel->created_at = date('Y-m-d H:i:s');
            $answerModel->save(false);

            $ticket->updateAttributes(['new_message_for_admin' => 1]);

            Yii::$app->session->setFlash('success', 'Ответ добавлен');
            return $this->refresh();
        }

        return $this->render('view', [
            'ticket'              => $ticket,
            'model'               => $answerModel,
            'answersDataProvider' => $dataProvider,
        ]);
    }

    /* ---------- Закрытие тикета ---------- */
    public function actionClose($ticket_id)
    {
        $ticket = Tickets::findOne(['id' => $ticket_id, 'user_id' => Yii::$app->user->id]);
        if ($ticket && $ticket->status == Tickets::STATUS_OPEN) {
            $ticket->status = Tickets::STATUS_CLOSED;
            $ticket->save(false);
            Yii::$app->session->setFlash('success', 'Тикет закрыт');
        }
        return $this->redirect(['index']);
    }

    /* ---------- Добавление ответа через POST ---------- */
    public function actionReply($ticket_id)
    {
        $ticket = Tickets::findOne(['id' => $ticket_id, 'user_id' => Yii::$app->user->id]);
        if (!$ticket) {
            throw new NotFoundHttpException('Тикет не найден.');
        }

        $model = new TicketsAnswers();
        $model->ticket_id  = $ticket->id;
        $model->user_id    = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->save(false);

            $ticket->updateAttributes(['new_message_for_admin' => 1]);

            Yii::$app->session->setFlash('success', 'Ответ добавлен.');
        }

        return $this->redirect(['view', 'ticket_id' => $ticket->id]);
    }
}