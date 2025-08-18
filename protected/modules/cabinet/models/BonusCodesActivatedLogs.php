<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class BonusCodesActivatedLogs extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bonus_codes_activated_logs}}';
    }

    public function rules()
    {
        return [
            [['code_id', 'user_id', 'created_at'], 'required'],
            [['code_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }
}