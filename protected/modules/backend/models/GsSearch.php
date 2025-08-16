<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class GsSearch extends Gs
{
    public function rules()
    {
        return [
            [['id', 'login_id', 'status'], 'integer'],
            [['name', 'ip', 'version', 'db_host', 'db_user', 'db_name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Gs::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['login_id' => $this->login_id]);
        $query->andFilterWhere(['status' => $this->status]);

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'ip', $this->ip])
              ->andFilterWhere(['like', 'version', $this->version])
              ->andFilterWhere(['like', 'db_host', $this->db_host])
              ->andFilterWhere(['like', 'db_user', $this->db_user])
              ->andFilterWhere(['like', 'db_name', $this->db_name]);

        return $dataProvider;
    }
}
