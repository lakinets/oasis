<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class UserBonuses extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_bonuses}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'bonus_id'], 'required'],
            [['user_id', 'bonus_id', 'status'], 'integer'],
        ];
    }

    public function getBonusInfo()
    {
        return $this->hasOne(Bonuses::class, ['id' => 'bonus_id']);
    }
}