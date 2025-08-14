<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TicketsSearch extends Tickets
{
    public $categoryTitle;
    public $userLogin;

    public function rules()
    {
        return [
            [['id', 'user_id', 'category_id', 'status', 'priority', 'new_message_for_admin'], 'integer'],
            [['title', 'text', 'created_at', 'updated_at', 'categoryTitle', 'userLogin'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Tickets::find()
            ->leftJoin('tickets_categories', 'tickets_categories.id = tickets.category_id')
            ->leftJoin('users', 'users.user_id = tickets.user_id')
            ->orderBy(['tickets.created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // фильтры
        $query->andFilterWhere(['tickets.id'          => $this->id])
              ->andFilterWhere(['tickets.status'      => $this->status])
              ->andFilterWhere(['tickets.priority'    => $this->priority])
              ->andFilterWhere(['tickets.category_id' => $this->category_id])
              ->andFilterWhere(['tickets.new_message_for_admin' => $this->new_message_for_admin])
              ->andFilterWhere(['like', 'tickets.title', $this->title])
              ->andFilterWhere(['like', 'tickets_categories.title', $this->categoryTitle])
              ->andFilterWhere(['like', 'users.login', $this->userLogin]);

        // фильтр по дате
        if ($this->created_at) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'tickets.created_at', $date, $date + 86400 - 1]);
        }

        return $dataProvider;
    }
}