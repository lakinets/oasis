<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class AllItems extends ActiveRecord
{
    public static function tableName()
    {
        return 'all_items'; // ваше имя таблицы
    }

    public function getIcon()
    {
        // логика иконки
        return '/icons/' . $this->icon . '.png';
    }
}