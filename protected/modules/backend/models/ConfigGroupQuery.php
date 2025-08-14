<?php
namespace app\modules\backend\models;

use yii\db\ActiveQuery;

class ConfigGroupQuery extends ActiveQuery
{
    public function opened()
    {
        return $this->andWhere(['status' => 1]);
    }
}