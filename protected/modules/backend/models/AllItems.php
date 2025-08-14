<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class AllItems extends ActiveRecord
{
    public static function tableName()
    {
        return 'all_items';
    }

    public function attributeLabels()
    {
        return [
            'name'        => 'Название',
            'description' => 'Описание',
            'icon'        => 'Иконка',
        ];
    }
}