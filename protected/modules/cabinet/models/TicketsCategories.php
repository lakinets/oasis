<?php
namespace app\modules\cabinet\models;

use yii\db\ActiveRecord;

class TicketsCategories extends ActiveRecord
{
    public static function tableName()
    {
        // таблица категорий тикетов
        return '{{%tickets_categories}}';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function getTickets()
    {
        return $this->hasMany(Tickets::class, ['category_id' => 'id']);
    }
}
