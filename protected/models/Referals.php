<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Модель рефералов (таблица `referals`)
 *
 * @property int $referer
 * @property int $referal
 * @property string $created_at
 */
class Referals extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%referals}}';
    }

    public function rules()
    {
        return [
            [['referer', 'referal'], 'required'],
            [['referer', 'referal'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function getReferalInfo()
    {
        return $this->hasOne(\app\models\User::class, ['user_id' => 'referal']);
    }
}