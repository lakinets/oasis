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

            // значения по умолчанию
            [['discount', 'sort'], 'default', 'value' => 0],
            [['enchant'], 'default', 'value' => 0],
        ];
    }

    /* ---------- связи ---------- */
    public function getPack()
    {
        return $this->hasOne(ShopItemsPacks::class, ['id' => 'pack_id']);
    }

    public function getItemInfo()
    {
        return $this->hasOne(AllItems::class, ['item_id' => 'item_id']);
    }

    /* ---------- подготовка к сохранению ---------- */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // подставляем 0, если не заполнено
        if ($this->discount === null || $this->discount === '') {
            $this->discount = 0;
        }
        if ($this->sort === null || $this->sort === '') {
            $this->sort = 0;
        }
        if ($this->enchant === null || $this->enchant === '') {
            $this->enchant = 0;
        }

        // описание: ручное или из all_items
        if (!$this->description) {
            $ai = AllItems::findOne($this->item_id);
            if ($ai && !empty($ai->description)) {
                $this->description = $ai->description;
            }
        }

        return true;
    }

    /**
     * Человеко-читаемое описание
     */
    public function getFullDescription(): string
    {
        return $this->description ?: ($this->itemInfo ? $this->itemInfo->description : '');
    }
}