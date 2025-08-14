<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class Bonuses extends ActiveRecord
{
    public static function tableName() { return 'bonuses'; }

    public function getItems()
    {
        return $this->hasMany(BonusesItems::class, ['bonus_id' => 'id']);
    }

    public function getItemCount()
    {
        return $this->hasOne(BonusesItems::class, ['bonus_id' => 'id'])
            ->select(['bonus_id', 'COUNT(*) as cnt'])
            ->groupBy('bonus_id');
    }

    public function getStatusLabel()
    {
        return $this->status ? 'Активен' : 'Не активен';
    }
}