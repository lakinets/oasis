<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * @property int    $id
 * @property string $title
 * @property int    $status
 * @property int    $sort
 */
class TicketsCategories extends ActiveRecord
{
    /* --- статусы --- */
    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    public static function tableName()
    {
        return 'tickets_categories';
    }

    public function rules()
    {
        return [
            [['title', 'sort'], 'required'],
            [['status', 'sort'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /* --- список статусов --- */
    public static function getStatusList()
    {
        return [
            self::STATUS_ON  => Yii::t('backend', 'Включена'),
            self::STATUS_OFF => Yii::t('backend', 'Выключена'),
        ];
    }

    public function getStatus()
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public function isStatusOn()
    {
        return $this->status == self::STATUS_ON;
    }
}