<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class BonusCodesSearch extends BonusCodes
{
    public function rules(): array
    {
        return [
            [['id', 'bonus_id', 'limit', 'status'], 'integer'],
            [['code'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = BonusCodes::find()->with('bonus');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'bonus_id' => $this->bonus_id,
            'limit' => $this->limit,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
