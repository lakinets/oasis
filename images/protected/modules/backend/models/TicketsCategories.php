<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class TicketsCategories extends ActiveRecord
{
    public static function tableName() { return 'tickets_categories'; }
}