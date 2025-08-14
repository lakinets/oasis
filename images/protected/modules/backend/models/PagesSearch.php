<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class PagesSearch extends Pages
{
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['title'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Pages::find()->andWhere(['!=', 'status', Pages::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }
}