<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name
 * @property float $cost
 * @property int $status
 * @property string $type
 */
class Services extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    /* ----------  типы услуг  ---------- */
    const TYPE_CHANGE_NAME   = 'change_name';
    const TYPE_CHANGE_GENDER = 'change_gender';
    const TYPE_REMOVE_KARMA  = 'remove_karma';
    const TYPE_NOBLE_STATUS  = 'noble_status';

    /* ----------  базовые  ---------- */
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
            [['type'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => self::STATUS_ENABLED],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'name'   => 'Имя сервиса',
            'cost'   => 'Стоимость',
            'status' => 'Статус',
            'type'   => 'Тип',
        ];
    }

    /* ----------  справочники  ---------- */
    public static function getActiveServices()
    {
        return self::find()
            ->where(['status' => self::STATUS_ENABLED])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    public function isAvailable()
    {
        return $this->status == self::STATUS_ENABLED;
    }
}