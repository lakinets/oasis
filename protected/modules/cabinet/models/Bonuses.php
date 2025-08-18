<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class Bonuses extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bonuses}}';
    }

    public function rules()
    {
        return [
            [['title', 'status'], 'required'],
            [['date_end'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['status'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function getItems()
    {
        return $this->hasMany(BonusesItems::class, ['bonus_id' => 'id']);
    }
}