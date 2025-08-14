<?php
namespace app\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GsSearch extends Gs
{
    public function rules()
    {
        return [
            [['name', 'db_name', 'db_host', 'db_user', 'db_pass'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Gs::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'db_name', $this->db_name])
              ->andFilterWhere(['like', 'db_host', $this->db_host])
              ->andFilterWhere(['like', 'db_user', $this->db_user])
              ->andFilterWhere(['like', 'db_pass', $this->db_pass]);

        return $dataProvider;
    }
}