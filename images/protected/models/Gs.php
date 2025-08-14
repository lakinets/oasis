<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%gs}}".
 *
 * @property int $id
 * @property string $name
 * @property string $ip
 * @property int $port
 * @property string $db_host
 * @property string $db_port
 * @property string $db_user
 * @property string $db_pass
 * @property string $db_name
 * @property int $login_id
 * @property string $version
 * @property string $fake_online
 * @property int $allow_teleport
 * @property string $teleport_time
 * @property int $stats_allow
 * @property string $stats_cache_time
 * @property int $stats_total
 * @property int $stats_pvp
 * @property int $stats_pk
 * @property int $stats_clans
 * @property int $stats_castles
 * @property int $stats_online
 * @property int $stats_clan_info
 * @property int $stats_top
 * @property int $stats_rich
 * @property string $stats_count_results
 * @property string $exp
 * @property string $sp
 * @property string $adena
 * @property string $drop
 * @property string $items
 * @property string $spoil
 * @property string $q_drop
 * @property string $q_reward
 * @property string $rb
 * @property string $erb
 * @property int $services_premium_allow
 * @property string $services_premium_cost
 * @property int $services_remove_hwid_allow
 * @property int $services_change_char_name_allow
 * @property int $services_change_char_name_cost
 * @property string $services_change_char_name_chars
 * @property int $services_change_gender_allow
 * @property int $services_change_gender_cost
 * @property string $currency_name
 * @property int $deposit_allow
 * @property int $deposit_payment_system
 * @property string $deposit_desc
 * @property int $deposit_course_payments
 * @property int $currency_symbol
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $stats_items
 * @property string $stats_items_list
 * @property int $online_txt_allow
 *
 * @property Ls $ls
 */
class Gs extends ActiveRecord
{
    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [array_keys($this->attributes), 'filter', 'filter' => 'trim'],
            [
                [
                    'name', 'ip', 'port', 'db_host', 'db_port', 'db_user', 'db_name',
                    'login_id', 'version', 'status', 'fake_online', 'allow_teleport',
                    'teleport_time', 'stats_allow', 'stats_cache_time', 'stats_total',
                    'stats_pvp', 'stats_pk', 'stats_clans', 'stats_castles', 'stats_online',
                    'stats_clan_info', 'stats_top', 'stats_rich', 'stats_count_results',
                    'exp', 'sp', 'adena', 'drop', 'items', 'spoil', 'q_drop', 'q_reward',
                    'rb', 'erb', 'services_premium_allow', 'currency_name', 'deposit_allow',
                    'deposit_payment_system', 'deposit_desc', 'deposit_course_payments',
                    'currency_symbol', 'stats_items', 'online_txt_allow'
                ],
                'required',
            ],
            [
                [
                    'port', 'db_port', 'login_id', 'fake_online', 'teleport_time',
                    'stats_cache_time', 'stats_total', 'stats_pvp', 'stats_pk',
                    'stats_clans', 'stats_castles', 'stats_online', 'stats_clan_info',
                    'stats_top', 'stats_rich', 'exp', 'sp', 'adena', 'drop', 'items',
                    'spoil', 'q_drop', 'q_reward', 'rb', 'erb', 'services_premium_allow',
                    'services_remove_hwid_allow', 'services_change_char_name_allow',
                    'services_change_gender_allow', 'deposit_allow', 'deposit_payment_system',
                    'deposit_course_payments', 'currency_symbol', 'stats_items', 'online_txt_allow'
                ],
                'integer',
            ],
            [['services_premium_cost'], 'safe'],
            [['services_premium_cost'], 'validatePremiumCost'],
            [['stats_items'], 'validateStatsItemsList'],
            [['login_id'], 'validateLoginExists'],
            ['status', 'in', 'range' => [self::STATUS_ON, self::STATUS_OFF]],
            ['version', 'in', 'range' => array_keys(\Yii::$app->params['server_versions'] ?? [])],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Связь с Ls
     */
    public function getLs()
    {
        return $this->hasOne(Ls::class, ['id' => 'login_id']);
    }

    /**
     * Scope: открытые серверы
     */
    public static function findOpened()
    {
        return self::find()->where(['status' => self::STATUS_ON]);
    }

    /**
     * Список открытых серверов с кэшем
     */
    public static function getOpenServers()
    {
        return \Yii::$app->cache->getOrSet('open_servers', function () {
            return self::findOpened()->with('ls')->all();
        }, 3600 * 24);
    }

    /**
     * Кодировка/раскодировка поля services_premium_cost
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->services_premium_cost) {
            $unserialized = @unserialize($this->services_premium_cost);
            $this->services_premium_cost = is_array($unserialized) ? $unserialized : [];
        }
    }

    public function beforeSave($insert)
    {
        if (is_array($this->services_premium_cost)) {
            $this->services_premium_cost = serialize($this->services_premium_cost);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => \Yii::t('backend', 'Название'),
            'ip' => \Yii::t('backend', 'IP адрес'),
            'port' => \Yii::t('backend', 'Порт'),
            // добавьте остальные поля при необходимости
        ];
    }

    /**
     * Custom query class
     */
    public static function find()
    {
        return new GsQuery(get_called_class());
    }
}