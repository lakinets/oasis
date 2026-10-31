<?php

namespace app\modules\backend\models;

use yii\db\ActiveRecord;

/**
 * @property int    $asn
 * @property string $description
 * @property string $country_code
 * @property string $country_en
 * @property string $country_ru
 * @property string $continent_en
 * @property string $continent_ru
 * @property int    $enabled
 */
class AsnWhitelist extends ActiveRecord
{
    public static function tableName()
    {
        return 'asn_whitelist';
    }

    public static function primaryKey()
    {
        return ['asn'];
    }

    public function rules()
    {
        return [
            [['asn', 'description', 'country_code'], 'required'],
            [['asn', 'enabled'], 'integer'],
            [['enabled'], 'in', 'range' => [0, 1]],
            [['description', 'country_en', 'country_ru', 'continent_en', 'continent_ru'], 'string'],
            [['country_code'], 'string', 'max' => 2],
        ];
    }

    public function attributeLabels()
    {
        return [
            'asn'          => 'ASN',
            'description'  => 'Провайдер',
            'country_code' => 'Код страны',
            'country_en'   => 'Страна (EN)',
            'country_ru'   => 'Страна (RU)',
            'continent_en' => 'Континент (EN)',
            'continent_ru' => 'Континент (RU)',
            'enabled'      => 'Включён',
        ];
    }
}