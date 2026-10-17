<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class ShopCategories extends ActiveRecord
{
    public static function tableName()
    {
        return 'shop_categories';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => fn() => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'link', 'sort', 'status', 'gs_id'], 'required'],
            [['sort', 'status', 'gs_id'], 'integer'],
            [['name', 'name_en', 'link'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'    => Yii::t('backend', 'Название (RU)'),
            'name_en' => Yii::t('backend', 'Название (EN)'),
            'link'    => Yii::t('backend', 'Ссылка'),
            'sort'    => Yii::t('backend', 'Порядок сортировки'),
            'status'  => Yii::t('backend', 'Активна'),
        ];
    }

    public function getPacks()
    {
        return $this->hasMany(ShopItemsPacks::class, ['category_id' => 'id']);
    }

    /**
     * Название категории с учётом языка.
     * Fallback: если EN пусто → показываем RU (для админки это удобнее, чем пустота).
     */
    public function getName($lang = null): string
    {
        $lang = $lang ?: Yii::$app->language;
        if ($lang === 'en' && $this->name_en !== null && $this->name_en !== '') {
            return (string)$this->name_en;
        }
        return (string)$this->name;
    }
}