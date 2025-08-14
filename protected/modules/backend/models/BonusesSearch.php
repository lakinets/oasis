<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class BonusesSearch extends Bonuses
{
    public $date_end_from;
    public $date_end_to;

    public function rules(): array
    {
        return [
            [['id', 'status'], 'integer'],
            [['title', 'date_end_from', 'date_end_to'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Bonuses::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // при ошибке валидации возвращаем пустой набор
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id, 'status' => $this->status]);
        $query->andFilterWhere(['like', 'title', $this->title]);

        if ($this->date_end_from) {
            $query->andWhere(['>=', 'date_end', $this->date_end_from]);
        }
        if ($this->date_end_to) {
            $query->andWhere(['<=', 'date_end', $this->date_end_to]);
        }

        return $dataProvider;
    }
}
