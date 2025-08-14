<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserBonusesSearch extends UserBonuses
{
    public $login;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['login', 'bonus_title', 'created_at'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = UserBonuses::find()->joinWith(['user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['login'] = [
            'asc'  => ['users.login' => SORT_ASC],
            'desc' => ['users.login' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['user_bonuses.id' => $this->id]);
        $query->andFilterWhere(['user_bonuses.status' => $this->status]);
        $query->andFilterWhere(['like', 'users.login', $this->login]);
        $query->andFilterWhere(['like', 'bonus_title', $this->bonus_title]);

        return $dataProvider;
    }
}
