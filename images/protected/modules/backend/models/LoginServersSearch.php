<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class LoginServersSearch extends LoginServers
{
    public function rules()
    {
        return [
            [['id', 'port', 'status'], 'integer'],
            [['name', 'host'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = LoginServers::find()->andWhere(['!=', 'status', LoginServers::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'host', $this->host])
              ->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}