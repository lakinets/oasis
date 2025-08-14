<?php

namespace app\models;

use yii\db\ActiveRecord;

class News extends ActiveRecord
{
    public static function tableName()
    {
        return 'news'; // Название таблицы в БД
    }

    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 255],
        ];
    }
}
