<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\models\Tickets;
use app\modules\cabinet\models\TicketsForm;
use app\modules\cabinet\models\TicketsAnswers;
use yii\data\ActiveDataProvider;

class TicketsController extends CabinetBaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Tickets::find()
                ->where(['user_id' => \Yii::$app->user->id])
                ->with(['category'])
                ->orderBy(['status' => SORT_DESC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.tickets.limit'] ?? 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdd()
    {
        $model = new TicketsForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $ticket = new Tickets([
                'user_id' => \Yii::$app->user->id,
                'category_id' => $model->category_id,
                'title' => $model->title,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $ticket->save();

            $answer = new TicketsAnswers([
                'ticket_id' => $ticket->id,
                'user_id' => \Yii::$app->user->id,
                'text' => $model->text,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $answer->save();

            \Yii::$app->session->setFlash('success', 'Тикет создан');
            return $this->redirect(['view', 'ticket_id' => $ticket->id]);
        }

        return $this->render('add', ['model' => $model]);
    }

    public function actionView($ticket_id)
    {
        $ticket = Tickets::findOne(['id' => $ticket_id, 'user_id' => \Yii::$app->user->id]);
        if (!$ticket) {
            throw new \yii\web\NotFoundHttpException();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => TicketsAnswers::find()
                ->where(['ticket_id' => $ticket->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.tickets.answers.limit'] ?? 5,
            ],
        ]);

        $model = new TicketsAnswers();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->ticket_id = $ticket->id;
            $model->user_id = \Yii::$app->user->id;
            $model->created_at = date('Y-m-d H:i:s');
            $model->save();
            \Yii::$app->session->setFlash('success', 'Ответ добавлен');
            return $this->refresh();
        }

        return $this->render('view', [
            'ticket' => $ticket,
            'model' => $model,
            'answersDataProvider' => $dataProvider,
        ]);
    }

    public function actionClose($ticket_id)
    {
        $ticket = Tickets::findOne(['id' => $ticket_id, 'user_id' => \Yii::$app->user->id]);
        if ($ticket && $ticket->status == 1) {
            $ticket->status = 0;
            $ticket->save(false);
            \Yii::$app->session->setFlash('success', 'Тикет закрыт');
        }
        return $this->redirect(['index']);
    }
}