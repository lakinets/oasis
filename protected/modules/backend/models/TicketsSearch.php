<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TicketsSearch extends Tickets
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'category_id', 'status', 'priority'], 'integer'],
            [['title', 'date_incident'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Tickets::find()
            ->leftJoin('tickets_categories', 'tickets_categories.id = tickets.category_id')
            ->leftJoin('users', 'users.user_id = tickets.user_id')
            ->orderBy(['tickets.created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tickets.id'          => $this->id,
            'tickets.status'      => $this->status,
            'tickets.priority'    => $this->priority,
            'tickets.category_id' => $this->category_id,
        ])->andFilterWhere(['like', 'tickets.title', $this->title]);

        return $dataProvider;
    }
}