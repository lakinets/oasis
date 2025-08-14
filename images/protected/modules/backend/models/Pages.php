<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class Pages extends ActiveRecord
{
    public const STATUS_ON  = 1;
    public const STATUS_OFF = 0;
    public const STATUS_DELETED = 2;

    public static function tableName()
    {
        return 'pages';
    }

    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_ON],
        ];
    }

    public function getStatusLabel()
    {
        return $this->status == self::STATUS_ON ? 'Активна' : 'Не активна';
    }
}