<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

class TicketsCategories extends ActiveRecord
{
    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    public static function tableName()
    {
        return '{{%tickets_categories}}';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['status', 'sort'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ON],
            [['sort'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'     => Yii::t('backend', 'ID'),
            'title'  => Yii::t('backend', 'Название'),
            'status' => Yii::t('backend', 'Статус'),
            'sort'   => Yii::t('backend', 'Сортировка'),
        ];
    }

    /**
     * Список статусов для dropDownList.
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_OFF => Yii::t('backend', 'Выключен'),
            self::STATUS_ON  => Yii::t('backend', 'Включен'),
        ];
    }

    /**
     * Включена ли категория (instance-метод).
     */
    public function isStatusOn(): bool
    {
        return (int)$this->status === self::STATUS_ON;
    }

    /**
     * Человекочитаемая метка статуса.
     */
    public function getStatusLabel(): string
    {
        return $this->isStatusOn()
            ? Yii::t('backend', 'Включен')
            : Yii::t('backend', 'Выключен');
    }

    /**
     * Список категорий [id => title] для дропдаунов (без локализации).
     */
    public static function list()
    {
        return static::find()
            ->select(['title', 'id'])
            ->indexBy('id')
            ->column();
    }

    /**
     * Список категорий [id => локализованный title] для дропдаунов.
     *
     * Если админ добавил свою категорию, ключа в messages/{lang}/backend.php
     * нет — Yii вернёт оригинальное значение (то, что ввёл админ).
     */
    public static function getDropdownList(): array
    {
        $titles = static::find()
            ->select(['title'])
            ->indexBy('id')
            ->column();

        $result = [];
        foreach ($titles as $id => $title) {
            $result[$id] = Yii::t('backend', $title);
        }

        return $result;
    }

    public function getTickets()
    {
        return $this->hasMany(Tickets::class, ['category_id' => 'id']);
    }
}