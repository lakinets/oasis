<?php

namespace app\models;

use yii\db\ActiveQuery;

class GsQuery extends ActiveQuery
{
    public function opened()
    {
        return $this->andWhere(['status' => 1]);
    }
}