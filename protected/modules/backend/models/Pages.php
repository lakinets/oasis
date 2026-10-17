<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

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
            [['page', 'title', 'text'], 'required'],
            [['page', 'title', 'seo_title', 'seo_keywords', 'seo_description'], 'string', 'max' => 255],
            [['text'], 'string'],
            [['status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ON],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'page' => Yii::t('backend', 'Имя страницы (page)'),
            'title' => Yii::t('backend', 'Заголовок'),
            'text' => Yii::t('backend', 'Содержимое'),
            'seo_title' => 'SEO title',
            'seo_keywords' => 'SEO keywords',
            'seo_description' => 'SEO description',
            'status' => Yii::t('backend', 'Статус'),
            'created_at' => Yii::t('backend', 'Создано'),
            'updated_at' => Yii::t('backend', 'Обновлено'),
        ];
    }

    public function getStatusLabel(): string
    {
        return $this->status == self::STATUS_ON ? Yii::t('backend', 'Активна') : Yii::t('backend', 'Не активна');
    }
}

