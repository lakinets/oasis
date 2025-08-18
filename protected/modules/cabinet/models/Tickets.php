<?php

namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class Tickets extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%tickets}}';
    }

    public function getCategory()
    {
        return $this->hasOne(TicketsCategories::class, ['id' => 'category_id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(TicketsAnswers::class, ['ticket_id' => 'id']);
    }
}