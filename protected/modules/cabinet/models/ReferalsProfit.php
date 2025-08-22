<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

/**
 * Таблица {{referals_profit}}
 * @property int $id
 * @property int $referer_id
 * @property int $user_id
 * @property int $profit
 * @property string $created_at
 */
class ReferalsProfit extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%referals_profit}}';
    }

    public function rules()
    {
        return [
            [['referer_id', 'user_id', 'profit'], 'required'],
            [['referer_id', 'user_id', 'profit'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\app\models\User::class, ['user_id' => 'user_id']);
    }
}