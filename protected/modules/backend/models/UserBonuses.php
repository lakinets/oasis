<?php
namespace app\modules\backend\models;

use Yii; // <--- ДОБАВЛЕНО
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
        return $this->hasOne(Users::class, ['user_id'=>'user_id']);
    }

    public function getStatusName()
    {
        return $this->status ? Yii::t('backend', 'Активирован') : Yii::t('backend', 'Не активирован');
    }
}