<?php
namespace app\modules\cabinet\models;

use Yii;
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

    /**
     * Список категорий [id => локализованный title] для дропдаунов.
     *
     * Используем категорию переводов 'backend', потому что это те же
     * категории, что и в админке. Если админ добавит свою категорию —
     * ключа в backend.php нет, Yii вернёт оригинал (то, что ввёл админ).
     */
    public static function getDropdownList(): array
    {
        $titles = static::find()
            ->select(['title'])
            ->indexBy('id')
            ->column();

        $result = [];
        foreach ($titles as $id => $title) {
            $result[$id] = Yii::t('backend', $title);
        }

        return $result;
    }
}