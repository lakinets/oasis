<?php
namespace app\modules\backend\models;

use Yii;
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
            [['description', 'description_en'], 'string'],
            [['created_at', 'updated_at'], 'safe'],

            [['discount', 'sort'], 'default', 'value' => 0],
            [['enchant'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'item_id'        => Yii::t('backend', 'ID предмета'),
            'description'    => Yii::t('backend', 'Описание (RU)'),
            'description_en' => Yii::t('backend', 'Описание (EN)'),
            'count'          => Yii::t('backend', 'Кол-во'),
            'enchant'        => Yii::t('backend', 'Заточка'),
            'cost'           => Yii::t('backend', 'Цена (Web Adena)'),
            'discount'       => Yii::t('backend', 'Скидка (%)'),
            'currency_type'  => Yii::t('backend', 'Валюта покупки'),
            'status'         => Yii::t('backend', 'Активен'),
            'sort'           => Yii::t('backend', 'Сортировка'),
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

        if ($this->discount === null || $this->discount === '') {
            $this->discount = 0;
        }
        if ($this->sort === null || $this->sort === '') {
            $this->sort = 0;
        }
        if ($this->enchant === null || $this->enchant === '') {
            $this->enchant = 0;
        }

        // Описание: если у предмета пусто — берём из all_items (для каждого языка отдельно).
        $needRu = !$this->description;
        $needEn = !$this->description_en;

        if ($needRu || $needEn) {
            $ai = AllItems::findOne($this->item_id);
            if ($ai) {
                if ($needRu && !empty($ai->description)) {
                    $this->description = $ai->description;
                }
                if ($needEn && !empty($ai->description_en)) {
                    $this->description_en = $ai->description_en;
                }
            }
        }

        return true;
    }

    /**
     * Описание предмета с учётом языка.
     *
     * Приоритет:
     *   1. shop_items.description_[lang]  — ручной override админа
     *   2. all_items.description_[lang]   — из игровой базы
     *   3. пусто (например, для Adena — нормально)
     *
     * @param string|null $lang 'ru' | 'en' | null (текущий язык приложения)
     */
    public function getDescription($lang = null): string
    {
        $lang = $lang ?: Yii::$app->language;
        $isEn = ($lang === 'en');

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

    /**
     * @deprecated Оставлен для обратной совместимости. Используйте getDescription().
     */
    public function getFullDescription(): string
    {
        return $this->getDescription();
    }
}