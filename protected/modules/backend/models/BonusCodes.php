<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $bonus_id
 * @property string $code
 * @property int $limit
 * @property int $status
 *
 * @property Bonuses $bonus
 */
class BonusCodes extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'bonus_codes';
    }

    public function rules(): array
    {
        return [
            [['bonus_id', 'code'], 'required'],
            [['bonus_id', 'limit', 'status'], 'integer'],
            [['code'], 'string', 'max' => 128],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'bonus_id' => 'Бонус',
            'code' => 'Код',
            'limit' => 'Лимит',
            'status' => 'Статус',
        ];
    }

    public function getBonus()
    {
        return $this->hasOne(Bonuses::class, ['id' => 'bonus_id']);
    }

    public function getStatusList(): array
    {
        return [0 => 'Выключен', 1 => 'Активирован'];
    }

    public function getStatusName(): string
    {
        return $this->getStatusList()[$this->status] ?? (string)$this->status;
    }
}
