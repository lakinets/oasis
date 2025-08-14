<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

class UserBonuses extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'user_bonuses';
    }

    public function rules(): array
    {
        return [
            [['bonus_id','user_id'],'required'],
            [['bonus_id','user_id','status'],'integer'],
            [['created_at','updated_at'],'safe']
        ];
    }

    public function getBonus()
    {
        return $this->hasOne(Bonuses::class, ['id'=>'bonus_id']);
    }

    public function getUser()
    {
        // твоя таблица users и модель Users есть в модуле
        return $this->hasOne(\app\modules\backend\models\Users::class, ['user_id'=>'user_id']);
    }

    public function getStatusName()
    {
        return $this->status ? 'Активирован' : 'Не активирован';
    }
}
