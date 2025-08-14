<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

/**
 * Модель для таблицы bonus_codes_activated_logs
 */
class BonusCodesActivatedLogs extends ActiveRecord
{
    public static function tableName()
    {
        return 'bonus_codes_activated_logs';
    }

    public function rules()
    {
        return [
            [['code_id', 'user_id'], 'required'],
            [['code_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => 'ID',
            'code_id'   => 'ID бонус кода',
            'user_id'   => 'ID пользователя',
            'created_at'=> 'Дата активации',
        ];
    }
}
