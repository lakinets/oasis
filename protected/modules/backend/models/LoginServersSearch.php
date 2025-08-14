<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class LoginServersSearch extends Model
{
    public $id;
    public $name;
    public $ip;
    public $port;
    public $status;
    public $version; // ✅ ВАЖНО: добавлено поле версии для фильтра

    public function rules(): array
    {
        return [
            [['id', 'port', 'status'], 'integer'],
            [['name', 'ip', 'version'], 'safe'], // ✅ Добавлено 'version'
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = LoginServers::find()
            ->andWhere(['!=', 'status', LoginServers::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'ip', $this->ip])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['version' => $this->version]); // ✅ Добавлен фильтр по версии

        return $dataProvider;
    }

    public function getStatusList(): array
    {
        return (new LoginServers())->getStatusList();
    }
}
