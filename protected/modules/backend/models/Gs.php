<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;

class Gs extends ActiveRecord
{
    public static function tableName()
    {
        return 'gs';
    }

    public function rules()
    {
        return [
            [['name', 'ip', 'port', 'login_id', 'version', 'db_host', 'db_port', 'db_user', 'db_name'], 'required'],

            [['port', 'db_port', 'login_id', 'status'], 'integer'],
            [['name', 'ip', 'db_host', 'db_user', 'db_pass', 'db_name', 'version', 'currency_name', 'currency_symbol'], 'string', 'max' => 255],
            [['shop_mode'], 'string', 'max' => 10],
            [['shop_mode'], 'in', 'range' => ['shop', 'topup']],

            [['services_premium_cost', 'deposit_desc', 'stats_items_list'], 'safe'],

            [['status'], 'default', 'value' => 1],
            [['port'], 'default', 'value' => 3306],
            [['shop_mode'], 'default', 'value' => 'shop'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('backend', 'ID'),
            'name'        => Yii::t('backend', 'Название'),
            'ip'          => Yii::t('backend', 'IP'),
            'port'        => Yii::t('backend', 'Порт'),
            'login_id'    => Yii::t('backend', 'Login-сервер'),
            'version'     => Yii::t('backend', 'Версия'),
            'db_host'     => Yii::t('backend', 'Хост БД'),
            'db_port'     => Yii::t('backend', 'Порт БД'),
            'db_user'     => Yii::t('backend', 'Пользователь БД'),
            'db_pass'     => Yii::t('backend', 'Пароль БД'),
            'db_name'     => Yii::t('backend', 'Название БД'),
            'status'      => Yii::t('backend', 'Статус'),
            'shop_mode'   => Yii::t('backend', 'Режим магазина'),
            'services_premium_cost' => Yii::t('backend', 'Стоимость премиума'),
        ];
    }

    public function isStatusOn(): bool
    {
        return (int)$this->status === 1;
    }

    public function getStatusLabel(): string
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public static function getStatusList(): array
    {
        return [
            0 => Yii::t('backend', 'Неактивен'),
            1 => Yii::t('backend', 'Активен'),
        ];
    }

    public static function getShopModeList(): array
    {
        return [
            'shop'  => Yii::t('backend', 'Магазин'),
            'topup' => Yii::t('backend', 'Пополнение'),
        ];
    }

    public static function getVersionList(): array
    {
        $path = Yii::getAlias('@app/../protected/modules/backend/components/versions');
        $files = glob($path . '/*.php');
        $versions = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                $v = basename($file, '.php');
                $versions[$v] = $v;
            }
            ksort($versions);
        }
        return $versions;
    }

    public function afterFind()
    {
        parent::afterFind();

        $raw = $this->getAttribute('services_premium_cost');
        if ($raw === null || $raw === '') {
            return;
        }

        $decoded = @unserialize($raw);
        if ($decoded !== false || $raw === 'b:0;') {
            $this->setAttribute('services_premium_cost', $decoded);
        } else {
            $this->setAttribute('services_premium_cost', []);
        }
    }

    public function beforeSave($insert)
    {
        $val = $this->getAttribute('services_premium_cost');
        if (is_array($val)) {
            $this->setAttribute('services_premium_cost', serialize($val));
        }
        return parent::beforeSave($insert);
    }

    public function getServicesPremiumCost(): array
    {
        $val = $this->getAttribute('services_premium_cost');
        return is_array($val) ? $val : [];
    }
}