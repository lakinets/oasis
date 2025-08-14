<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class LoginServers extends ActiveRecord
{
    public const STATUS_ON  = 1;
    public const STATUS_OFF = 0;
    public const STATUS_DELETED = 2;

    public static function tableName()
    {
        return 'login_servers';
    }

    public function rules()
    {
        return [
            [['name', 'host', 'port'], 'required'],
            [['name', 'host'], 'string', 'max' => 255],
            [['port'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ON],
        ];
    }

    public function getStatusLabel()
    {
        return $this->status == self::STATUS_ON ? 'Активен' : 'Не активен';
    }
}