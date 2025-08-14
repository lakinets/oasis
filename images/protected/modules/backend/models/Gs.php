<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class Gs extends ActiveRecord
{
    public static function tableName() { return 'gs'; }

    public function search()
    {
        return new \yii\data\ActiveDataProvider([
            'query' => static::find()->orderBy(['id' => SORT_ASC]),
        ]);
    }
}