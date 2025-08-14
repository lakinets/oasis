<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 * @property int $status
 * @property string|null $date_end
 *
 * Relations:
 * @property BonusCodes[] $codes
 */
class Bonuses extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function tableName(): string
    {
        return 'bonuses';
    }

    public function rules(): array
    {
        return [
            [['title'], 'required'],
            [['status'], 'integer'],
            [['date_end'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'status' => 'Статус',
            'date_end' => 'Дата окончания',
        ];
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_INACTIVE => 'Выключен',
            self::STATUS_ACTIVE => 'Активирован',
        ];
    }

    public function getStatusName(): string
    {
        return static::getStatusList()[$this->status] ?? (string)$this->status;
    }

    public function getCodes()
    {
        return $this->hasMany(BonusCodes::class, ['bonus_id' => 'id']);
    }

    public function getDateEndFormatted()
    {
        return $this->date_end
            ? Yii::$app->formatter->asDatetime($this->date_end)
            : '-';
    }
}
