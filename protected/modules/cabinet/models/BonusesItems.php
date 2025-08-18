<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class BonusesItems extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bonuses_items}}';
    }

    public function rules()
    {
        return [
            [['item_id', 'count', 'enchant', 'bonus_id', 'status'], 'required'],
            [['item_id', 'count', 'enchant', 'bonus_id', 'status'], 'integer'],
        ];
    }

    public function getBonus()
    {
        return $this->hasOne(Bonuses::class, ['id' => 'bonus_id']);
    }

    public function getItemInfo()
    {
        return $this->hasOne(\app\models\AllItems::class, ['item_id' => 'item_id']);
    }
}