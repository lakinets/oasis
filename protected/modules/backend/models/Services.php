<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name Имя сервиса
 * @property float $cost Стоимость сервиса
 * @property int $status 1-включен, 0-выключен
 */
class Services extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    public static function tableName()
    {
        return 'services';
    }

    public function rules()
    {
        return [
            [['name', 'cost'], 'required'],
            [['cost'], 'number', 'min' => 0],
            [['status'], 'integer', 'min' => 0, 'max' => 1],
            [['name'], 'string', 'max' => 100],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя сервиса',
            'cost' => 'Стоимость',
            'status' => 'Статус',
        ];
    }
}