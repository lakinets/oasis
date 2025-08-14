<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $order
 *
 * @property Config[] $configs
 */
class ConfigGroup extends ActiveRecord
{
    public static function tableName()
    {
        return 'config_group';
    }

    public function getConfigs(): ActiveQuery
    {
        return $this->hasMany(Config::class, ['group_id' => 'id'])
                    ->orderBy(['order' => SORT_ASC]);
    }

    public static function find()
    {
        return new ConfigGroupQuery(get_called_class());
    }

    public static function findOpened()
    {
        return static::find()->where(['status' => 1]);
    }
}