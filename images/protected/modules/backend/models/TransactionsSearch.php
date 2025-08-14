<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TransactionsSearch extends Transactions
{
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['aggregator', 'status'], 'string', 'max' => 50],
            [['amount'], 'number'],
            [['created_at'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params)
    {
        $query = Transactions::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere(['user_id' => $this->user_id])
              ->andFilterWhere(['like', 'aggregator', $this->aggregator])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['amount' => $this->amount])
              ->andFilterWhere(['DATE(created_at)' => $this->created_at]);

        return $dataProvider;
    }
}