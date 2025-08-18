<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class UsersAuthLogs extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%users_auth_logs}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'ip', 'created_at'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['ip'], 'string', 'max' => 25],
            [['user_agent'], 'string', 'max' => 255],
        ];
    }
}