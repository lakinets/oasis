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
            'page' => 'Имя страницы (page)',
            'title' => 'Заголовок',
            'text' => 'Содержимое',
            'seo_title' => 'SEO title',
            'seo_keywords' => 'SEO keywords',
            'seo_description' => 'SEO description',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    public function getStatusLabel(): string
    {
        return $this->status == self::STATUS_ON ? 'Активна' : 'Не активна';
    }
}
