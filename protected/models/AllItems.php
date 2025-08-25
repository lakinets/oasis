<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Таблица с описаниями игровых предметов, используемая магазином
 *
 * @property int    $item_id
 * @property string $name
 * @property string $add_name
 * @property string $description
 * @property string|null $icon   // относительный путь внутри /images/items
 * @property int    $crystal_type
 */
class AllItems extends ActiveRecord
{
    public static function tableName()
    {
        // Если у тебя таблица называется ghtweb_all_items — просто замени имя
        return '{{%all_items}}';
        // return '{{%ghtweb_all_items}}';
    }

    public static function primaryKey()
    {
        return ['item_id'];
    }

    public function rules()
    {
        return [
            [['item_id', 'name', 'crystal_type'], 'required'],
            [['item_id', 'crystal_type'], 'integer'],
            [['add_name', 'description'], 'string'],
            [['name', 'icon'], 'string', 'max' => 255],
        ];
    }

    /** URL иконки (с фолбэком /images/items/no-image.jpg) */
    public function getIconUrl(): string
    {
        $base = '/images/items/';
        $webroot = Yii::getAlias('@webroot') . $base;

        if ($this->icon && is_file($webroot . $this->icon)) {
            return $base . $this->icon;
        }
        // иногда в БД могут лежать полные пути/подпапки
        if ($this->icon && is_file(Yii::getAlias('@webroot') . '/' . ltrim($this->icon, '/'))) {
            return '/' . ltrim($this->icon, '/');
        }
        return '/images/items/no-image.jpg';
    }
}
