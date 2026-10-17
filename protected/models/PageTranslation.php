<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Перевод страницы (одна запись = один язык одной страницы).
 *
 * @property int    $id
 * @property int    $page_id
 * @property string $language
 * @property string $title
 * @property string $text
 * @property string $seo_title
 * @property string $seo_keywords
 * @property string $seo_description
 */
class PageTranslation extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%page_translations}}';
    }

    public function rules()
    {
        return [
            // page_id НЕ required: он присваивается в контроллере перед save(),
            // после того как Parent Page уже сохранён и получил id.
            [['language'], 'required'],
            [['page_id'], 'integer'],
            [['language'], 'string', 'max' => 10],
            [['title', 'seo_title', 'seo_keywords', 'seo_description'], 'string', 'max' => 255],
            [['text'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'page_id'         => Yii::t('backend', 'Страница'),
            'language'        => Yii::t('backend', 'Язык'),
            'title'           => Yii::t('backend', 'Заголовок'),
            'text'            => Yii::t('backend', 'Содержимое'),
            'seo_title'       => 'SEO title',
            'seo_keywords'    => 'SEO keywords',
            'seo_description' => 'SEO description',
        ];
    }
}