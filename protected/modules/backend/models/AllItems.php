<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

class AllItems extends ActiveRecord
{
    public static function tableName()
    {
        return 'all_items';
    }

    public function attributeLabels()
    {
        return [
            'name'           => Yii::t('backend', 'Название'),
            'description'    => Yii::t('backend', 'Описание (RU)'),
            'description_en' => Yii::t('backend', 'Описание (EN)'),
            'icon'           => Yii::t('backend', 'Иконка'),
        ];
    }
}