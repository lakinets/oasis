<?php
namespace app\models;

use yii\db\ActiveRecord;

class GiftCodeAttempt extends ActiveRecord
{
    public static function tableName()
    {
        return 'gift_codes_attempts';
    }
}