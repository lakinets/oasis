<?php

namespace app\modules\cabinet\models;

use Yii;
use yii\db\ActiveRecord;

class ShopItems extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%shop_items}}';
    }

    public function rules()
    {
        return [
            [['pack_id', 'item_id', 'count', 'enchant', 'status'], 'required'],
            [['pack_id', 'item_id', 'count', 'enchant', 'status', 'sort'], 'integer'],
            [['cost', 'discount'], 'number'],
            [['currency_type'], 'string', 'max' => 54],
            [['description', 'description_en'], 'string'],
        ];
    }

    public function getItemInfo()
    {
        return $this->hasOne(\app\models\AllItems::class, ['item_id' => 'item_id']);
    }

    public function getPack()
    {
        return $this->hasOne(ShopItemsPacks::class, ['id' => 'pack_id']);
    }

    /**
     * Описание предмета с учётом языка.
     *
     * Приоритет:
     *   1. shop_items.description_[lang]  — override админа
     *   2. all_items.description_[lang]   — из игровой базы
     *   3. пусто
     */
    public function getDescription($lang = null): string
    {
        $lang  = $lang ?: Yii::$app->language;
        $isEn  = ($lang === 'en');

        $own = $isEn ? $this->description_en : $this->description;
        if ($own !== null && $own !== '') {
            return (string)$own;
        }

        $ai = $this->itemInfo;
        if ($ai) {
            $fromAi = $isEn ? $ai->description_en : $ai->description;
            if ($fromAi !== null && $fromAi !== '') {
                return (string)$fromAi;
            }
        }

        return '';
    }
}