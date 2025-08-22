<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Модель игрового сервера (gs)
 *
 * @property int    $id
 * @property string $name
 * @property string $ip
 * @property int    $port
 * @property string $db_host
 * @property string $db_port
 * @property string $db_user
 * @property string $db_pass
 * @property string $db_name
 * @property int    $login_id
 * @property string $version        // имя файла без .php в /protected/l2j/
 * @property int    $fake_online
 * @property int    $stats_allow
 * @property int    $stats_cache_time
 * @property int    $stats_total
 * @property int    $stats_pvp
 * @property int    $stats_pk
 * @property int    $stats_clans
 * @property int    $stats_castles
 * @property int    $stats_online
 * @property int    $stats_clan_info
 * @property int    $stats_top
 * @property int    $stats_rich
 * @property int    $stats_count_results
 * @property int    $stats_items
 * @property string $stats_items_list
 * @property int    $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Login  $login
 */
class Gs extends ActiveRecord
{
    const STATUS_ON  = 1;
    const STATUS_OFF = 0;

    public static function tableName()
    {
        return '{{%gs}}';
    }

    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        // список доступных версий из /protected/l2j/*.php
        $versions = array_map('basename', glob(Yii::getAlias('@app/l2j/*.php')));

        return [
            [
                [
                    'name', 'ip', 'port',
                    'db_host', 'db_port', 'db_user', 'db_name',
                    'login_id', 'version', 'status'
                ],
                'required',
            ],

            [['port', 'login_id', 'status'], 'integer'],
            [['version'], 'in', 'range' => $versions],

            [['stats_cache_time', 'stats_count_results', 'fake_online'], 'string', 'max' => 20],

            [
                [
                    'stats_total', 'stats_pvp', 'stats_pk',
                    'stats_clans', 'stats_castles', 'stats_online',
                    'stats_clan_info', 'stats_top', 'stats_rich',
                    'stats_items', 'online_txt_allow'
                ],
                'boolean', 'trueValue' => 1, 'falseValue' => 0,
            ],

            [['stats_items_list'], 'string', 'max' => 255],
            [['login_id'], 'exist', 'targetClass' => Login::class, 'targetAttribute' => 'id'],
        ];
    }

    public function getLogin()
    {
        return $this->hasOne(Login::class, ['id' => 'login_id']);
    }

    /**
     * Активные серверы
     */
    public static function findOpened()
    {
        return self::find()->where(['status' => self::STATUS_ON]);
    }

    /**
     * Кэшированный список активных серверов
     */
    public static function getOpenServers()
    {
        return Yii::$app->cache->getOrSet('open_servers', function () {
            return self::findOpened()->with('login')->all();
        }, 3600 * 24);
    }

    /* ---------- Сериализация / десериализация ---------- */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->services_premium_cost) {
            $this->services_premium_cost = @unserialize($this->services_premium_cost) ?: [];
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
     * Человеко-читаемые подписи
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'name'               => 'Название',
            'ip'                 => 'IP',
            'port'               => 'Порт',
            'db_host'            => 'Хост БД',
            'db_port'            => 'Порт БД',
            'db_user'            => 'Пользователь БД',
            'db_pass'            => 'Пароль БД',
            'db_name'            => 'Название БД',
            'login_id'           => 'Login-сервер',
            'version'            => 'Тип сервера',
            'status'             => 'Статус',
            'fake_online'        => 'Фейковый онлайн',
            'stats_cache_time'   => 'Время кэша (мин)',
            'stats_count_results'=> 'Кол-во результатов',
            'stats_total'        => 'Общая статистика',
            'stats_pvp'          => 'Топ PvP',
            'stats_pk'           => 'Топ PK',
            'stats_clans'        => 'Топ кланов',
            'stats_castles'      => 'Замки',
            'stats_online'       => 'Онлайн',
            'stats_clan_info'    => 'Инфо о клане',
            'stats_top'          => 'Топ уровней',
            'stats_rich'         => 'Богачи',
            'stats_items'        => 'Предметы',
            'stats_items_list'   => 'Список предметов',
            'online_txt_allow'   => 'Онлайн из файла',
        ];
    }

    public static function find()
    {
        return new GsQuery(get_called_class());
    }
}