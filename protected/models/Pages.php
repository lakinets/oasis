<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель страницы (таблица `pages`).
 *
 * Ожидаемые поля в таблице (минимум):
 *  - id
 *  - page      (строка, уникальный ключ, например 'main', 'about' и т.д.)
 *  - title
 *  - content
 *  - status    (1 = опубликовано, 0 = скрыто)
 *  - created_at, updated_at (опционально)
 */
class Pages extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * Правила валидации.
     */
    public function rules()
    {
        return [
            [['page'], 'required'],
            [['content'], 'string'],
            [['status'], 'integer'],
            [['page', 'title'], 'string', 'max' => 255],
        ];
    }

    /**
     * Метки атрибутов.
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'page' => Yii::t('app', 'Page'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Удобный скоуп для опубликованных страниц.
     * Пример использования: Pages::find()->published()->where(...)->all();
     */
    public static function find()
    {
        return parent::find()->alias('p');
    }

    /**
     * По желанию: helper для получения опубликованной страницы по slug'у.
     */
    public static function getPublishedBySlug(string $slug)
    {
        return static::find()->where(['page' => $slug, 'status' => 1])->one();
    }
}
