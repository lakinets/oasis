<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class UserMessages extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_messages}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'title', 'text'], 'required'],
            [['user_id', 'read'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
        ];
    }
}