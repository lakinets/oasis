<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ShopItems extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_items';
    }

    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['pack_id', 'item_id', 'count', 'cost'], 'required'],
            [['pack_id', 'item_id', 'count', 'enchant', 'sort', 'status'], 'integer'],
            [['cost', 'discount'], 'number'],
            [['currency_type'], 'string', 'max' => 54],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /* ---------- Связи ---------- */
    public function getPack()
    {
        return $this->hasOne(ShopItemsPacks::class, ['id' => 'pack_id']);
    }

    public function getItemInfo()
    {
        return $this->hasOne(AllItems::class, ['item_id' => 'item_id']);
    }

    /* ---------- Логика описания ---------- */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // Если в форме не ввели описание, берём из all_items
        if ($this->description === null || $this->description === '') {
            $ai = AllItems::findOne($this->item_id);
            if ($ai && !empty($ai->description)) {
                $this->description = $ai->description;
            }
        }
        return true;
    }

    /**
     * Человеко-читаемое описание: ручное или из all_items
     */
    public function getFullDescription(): string
    {
        return $this->description ?: ($this->itemInfo ? $this->itemInfo->description : '');
    }
}