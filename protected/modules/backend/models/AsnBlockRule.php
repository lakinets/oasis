<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property string $rule_type   'continent' | 'country'
 * @property string $rule_value
 * @property int    $enabled
 * @property int    $updated_at
 */
class AsnBlockRule extends ActiveRecord
{
    const TYPE_CONTINENT = 'continent';
    const TYPE_COUNTRY   = 'country';

    public static function tableName()
    {
        return 'asn_block_rules';
    }

    public function rules()
    {
        return [
            [['rule_type', 'rule_value', 'enabled', 'updated_at'], 'required'],
            [['rule_type'], 'in', 'range' => [self::TYPE_CONTINENT, self::TYPE_COUNTRY]],
            [['rule_value'], 'string', 'max' => 64],
            [['enabled'], 'in', 'range' => [0, 1]],
            [['enabled', 'updated_at'], 'integer'],
        ];
    }

    /**
     * Возвращает true, если уровень разрешён.
     * Отсутствие правила = разрешено.
     */
    public static function isEnabled(string $type, string $value): bool
    {
        $row = static::findOne(['rule_type' => $type, 'rule_value' => $value]);
        return $row === null ? true : (int)$row->enabled === 1;
    }
}