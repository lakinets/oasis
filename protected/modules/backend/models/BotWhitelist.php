<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property string $name
 * @property string $ip_cidr
 * @property int    $enabled
 * @property int    $created_at
 * @property int    $updated_at
 */
class BotWhitelist extends ActiveRecord
{
    public static function tableName()
    {
        return 'bot_whitelist';
    }

    public function rules()
    {
        return [
            [['name', 'ip_cidr'], 'required'],
            [['enabled'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['ip_cidr'], 'string', 'max' => 64],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }
}