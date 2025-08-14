<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class Config extends ActiveRecord
{
    public static function tableName()
    {
        return 'config';
    }

    public function rules()
    {
        return [
            [['param'], 'required'],
            [['param', 'value', 'label', 'options'], 'string'],
            [['param'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'param' => Yii::t('backend', 'Параметр'),
            'value' => Yii::t('backend', 'Значение'),
            'label' => Yii::t('backend', 'Название'),
        ];
    }

    /* ---------- универсальные списки ---------- */

    public function getListOptions(): array
    {
        if (!empty($this->method) && method_exists($this, $this->method)) {
            return $this->{$this->method}();
        }
        return $this->field_type === 'dropDownList'
            ? [0 => 'Нет', 1 => 'Да']
            : [];
    }

    /* ---------- конкретные списки ---------- */

    public function getThemes(): array
    {
        $root = Yii::getAlias('@themesRoot');
        $items = [];
        if (is_dir($root)) {
            foreach (scandir($root) as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($root . DIRECTORY_SEPARATOR . $dir)) {
                    $items[$dir] = $dir;
                }
            }
        }
        return empty($items) ? ['ghtweb' => 'ghtweb'] : $items;
    }

    public function getIndexPageTypes(): array
    {
        return ['page' => 'Страница', 'news' => 'Новости'];
    }

    public function getPages(): array
    {
        return ['main' => 'main']; // при необходимости подключить реальные страницы
    }

    public function getForumTypes(): array
    {
        return ['phpbb' => 'phpBB', 'ipb' => 'IP.Board', 'smf' => 'SMF'];
    }

    public function getGs(): array
    {
        return [1 => 'Server 1']; // при необходимости подключить реальные серверы
    }

    /* ---------- генерация поля ---------- */

    public function getField(): string
    {
        $inputName = "Config[{$this->id}][value]";
        $value     = (string)$this->value;

        /* 1. темы */
        if ($this->param === 'theme') {
            return Html::dropDownList($inputName, $value, $this->getThemes(), ['class' => 'form-control']);
        }

        /* 2. формат даты новостей */
        if ($this->param === 'date_format' || $this->param === 'news.date_format') {
            return Html::dropDownList($inputName, $value, [
                'Y-m-d H:i'     => '2025-08-11 21:30',
                'd.m.Y H:i'     => '11.08.2025 21:30',
                'd/m/Y H:i'     => '11/08/2025 21:30',
                'd.m.Y'         => '11.08.2025',
                'd/m/Y'         => '11/08/2025',
                'j F Y, H:i'    => '11 августа 2025, 21:30',
                'D, M j, Y H:i' => 'Sun, Aug 11, 2025 21:30',
                'c'             => '2025-08-11T21:30:00+03:00',
            ], ['class' => 'form-control']);
        }

        /* 3. формат даты RSS */
        if ($this->param === 'index.rss.date_format') {
            return Html::dropDownList($inputName, $value, [
                'Y-m-d H:i'     => '2025-08-11 21:30',
                'd.m.Y H:i'     => '11.08.2025 21:30',
                'd/m/Y H:i'     => '11/08/2025 21:30',
                'd.m.Y'         => '11.08.2025',
                'd/m/Y'         => '11/08/2025',
                'j F Y, H:i'    => '11 августа 2025, 21:30',
                'D, M j, Y H:i' => 'Sun, Aug 11, 2025 21:30',
                'c'             => '2025-08-11T21:30:00+03:00',
            ], ['class' => 'form-control']);
        }

        /* 4. выпадающие списки по field_type */
        if ($this->field_type === 'dropDownList') {
            return Html::dropDownList($inputName, $value, $this->getListOptions(), ['class' => 'form-control']);
        }

        /* 5. остальные типы */
        switch ($this->field_type) {
            case 'textarea':
                return Html::textarea($inputName, $value, ['class' => 'form-control', 'rows' => 4]);

            case 'passwordField':
                return Html::passwordInput($inputName, $value, ['class' => 'form-control']);

            case 'textField':
            default:
                return Html::textInput($inputName, $value, ['class' => 'form-control']);
        }
    }
}