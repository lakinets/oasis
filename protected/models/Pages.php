<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель страницы (таблица `pages`).
 *
 * Поля:
 *  - id
 *  - page      (уникальный ключ, например 'main', 'about')
 *  - title     (fallback для sourceLanguage, используется если переводов нет)
 *  - text      (fallback, см. выше)
 *  - seo_title, seo_keywords, seo_description (fallback SEO)
 *  - status    (1 = опубликовано, 0 = скрыто, 2 = удалено)
 *  - created_at, updated_at
 *
 * Переводы хранятся в таблице `page_translations` (модель PageTranslation).
 */
class Pages extends ActiveRecord
{
    public const STATUS_ON      = 1;
    public const STATUS_OFF     = 0;
    public const STATUS_DELETED = 2;

    public static function tableName()
    {
        return 'pages';
    }

    public function rules()
    {
        return [
            [['page'], 'required'],
            [['text'], 'string'],
            [['status'], 'integer'],
            [['page', 'title', 'seo_title', 'seo_keywords', 'seo_description'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'page'            => Yii::t('models', 'Имя страницы (page)'),
            'title'           => Yii::t('models', 'Заголовок'),
            'text'            => Yii::t('models', 'Содержимое'),
            'seo_title'       => 'SEO title',
            'seo_keywords'    => 'SEO keywords',
            'seo_description' => 'SEO description',
            'status'          => Yii::t('models', 'Статус'),
            'created_at'      => Yii::t('models', 'Создано'),
            'updated_at'      => Yii::t('models', 'Обновлено'),
        ];
    }

    /**
     * Связь с переводами.
     */
    public function getTranslations()
    {
        return $this->hasMany(PageTranslation::class, ['page_id' => 'id']);
    }

    /**
     * Возвращает перевод для указанного языка с fallback на sourceLanguage.
     *
     * @param string|null $lang
     * @return PageTranslation|null
     */
    public function getTranslation($lang = null)
    {
        $lang   = $lang ?: Yii::$app->language;
        $source = Yii::$app->sourceLanguage;

        $translations = $this->translations ?: [];

        // 1. Искомый язык
        foreach ($translations as $t) {
            if ($t->language === $lang) {
                return $t;
            }
        }

        // 2. Fallback на sourceLanguage
        if ($lang !== $source) {
            foreach ($translations as $t) {
                if ($t->language === $source) {
                    return $t;
                }
            }
        }

        return null;
    }

    /**
     * Опубликованная страница по slug'у.
     */
    public static function getPublishedBySlug(string $slug)
    {
        return static::find()
            ->where(['page' => $slug, 'status' => self::STATUS_ON])
            ->one();
    }
}