<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class UserProfiles extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_profiles}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'balance', 'vote_balance'], 'default', 'value' => 0],
            [['protected_ip'], 'each', 'rule' => ['ip']],
            [['preferred_language'], 'string', 'max' => 5],
            [['phone'], 'string', 'max' => 20],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'user_id']);
    }
}