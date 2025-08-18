<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class BonusCodes extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bonus_codes}}';
    }

    public function rules()
    {
        return [
            [['code', 'bonus_id', 'limit', 'status'], 'required'],
            [['limit', 'bonus_id', 'status'], 'integer'],
            [['code'], 'string', 'max' => 128],
        ];
    }

    public function getBonusInfo()
    {
        return $this->hasOne(Bonuses::class, ['id' => 'bonus_id']);
    }

    public function getBonusLog()
    {
        return $this->hasMany(BonusCodesActivatedLogs::class, ['code_id' => 'id']);
    }
}