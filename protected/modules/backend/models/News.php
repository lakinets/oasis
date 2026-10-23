<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель новостей.
 */
class News extends ActiveRecord
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    const STATUS_DELETED = -1;

    public static function tableName()
    {
        return 'news';
    }

    public function rules()
    {
        return [
            [['title', 'text'], 'required'],
            [['description', 'text', 'description_en', 'text_en'], 'string'],
            [['title', 'title_en'], 'string', 'max' => 255],
            [['status', 'user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'user_id'        => Yii::t('models', 'Автор (ID)'),
            'title'          => Yii::t('models', 'Заголовок (RU)'),
            'description'    => Yii::t('models', 'Краткое описание (RU)'),
            'text'           => Yii::t('models', 'Текст новости (RU)'),
            'title_en'       => Yii::t('models', 'Заголовок (EN)'),
            'description_en' => Yii::t('models', 'Краткое описание (EN)'),
            'text_en'        => Yii::t('models', 'Текст новости (EN)'),
            'status'         => Yii::t('models', 'Активная новость'),
            'created_at'     => Yii::t('models', 'Дата создания'),
            'updated_at'     => Yii::t('models', 'Дата обновления'),
        ];
    }

    /**
     * Возвращает массив статусов для выпадающих списков
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_ON  => Yii::t('backend', 'Активна'),
            self::STATUS_OFF => Yii::t('backend', 'Не активна'),
        ];
    }

    /**
     * Возвращает текстовое название текущего статуса
     */
    public function getStatusLabel(): string
    {
        return self::getStatusList()[$this->status] ?? 'Неизвестно';
    }
}