<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class BonusesItems extends ActiveRecord
{
    public static function tableName()
    {
        return 'bonuses_items'; // имя таблицы в БД
    }
}
