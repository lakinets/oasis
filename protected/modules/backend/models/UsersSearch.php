<?php
namespace app\modules\backend\models;

use yii\data\ActiveDataProvider;

class UsersSearch extends Users
{
    public function rules(): array
    {
        return [
            [['user_id', 'activated'], 'integer'],
            [['login', 'email', 'role'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['user_id' => SORT_DESC],
                'attributes' => [
                    'user_id',
                    'login',
                    'email',
                    'role',
                    'activated',
                    'created_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'activated' => $this->activated,
        ]);

        $query->andFilterWhere(['like', 'login', $this->login])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'role', $this->role]);

        return $dataProvider;
    }
}
