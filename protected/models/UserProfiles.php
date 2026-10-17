<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Профиль пользователя.
 *
 * @property int    $id
 * @property int    $user_id
 * @property int    $balance
 * @property int    $vote_balance
 * @property string $preferred_language
 * @property string $protected_ip
 * @property string $phone
 *
 * @property Users  $user
 */
class UserProfiles extends ActiveRecord
{
    const DEFAULT_BALANCE      = 0;
    const DEFAULT_VOTE_BALANCE = 0;

    public static function tableName()
    {
        return '{{%user_profiles}}';
    }

    public function rules()
    {
        return [
            [['balance'], 'required'],
            [['balance', 'vote_balance', 'user_id'], 'integer'],
            [['balance'], 'string', 'max' => 10],
            [['protected_ip'], 'isValidIp', 'on' => 'security'],
            [['id', 'user_id', 'balance'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'user_id'            => Yii::t('models', 'Юзер'),
            'balance'            => Yii::t('models', 'Баланс'),
            'vote_balance'       => Yii::t('models', 'Голоса'),
            'preferred_language' => Yii::t('models', 'Предпочитаемый язык'),
            'protected_ip'       => Yii::t('models', 'IP адрес(а)'),
            'phone'              => Yii::t('models', 'Телефон'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

    /**
     * Возвращает профиль пользователя.
     * Если профиля нет — создаёт его с балансом 0.
     *
     * Это единственный правильный способ получать профиль.
     * Везде, где раньше было UserProfiles::findOne(['user_id' => X]),
     * теперь должно быть UserProfiles::getOrCreate(X).
     *
     * @param int $userId
     * @return self
     */
    public static function getOrCreate(int $userId): self
    {
        $profile = static::findOne(['user_id' => $userId]);
        if ($profile) {
            return $profile;
        }

        $profile = new static();
        $profile->user_id            = $userId;
        $profile->balance            = self::DEFAULT_BALANCE;
        $profile->vote_balance       = self::DEFAULT_VOTE_BALANCE;
        $profile->preferred_language = Yii::$app->language ?: 'ru';

        // save(false) — пропускаем валидацию, чтобы не спотыкаться о 'balance' => 'required'
        // при пограничных сценариях (например, если значение 0 и валидатор его отвергнет).
        // Все поля мы задали явно — валидация не нужна.
        $profile->save(false);

        return $profile;
    }

    /**
     * Проверка IP-адреса(ов).
     */
    public function isValidIp()
    {
        if ($this->protected_ip) {
            $ipList = is_array($this->protected_ip)
                ? $this->protected_ip
                : explode("\r\n", $this->protected_ip);

            foreach ($ipList as $ip) {
                if (!ip2long(trim($ip))) {
                    $this->addError(
                        'protected_ip',
                        Yii::t('models', '{bad_ip} - не является верным IP адресом.', ['bad_ip' => $ip])
                    );
                }
            }
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->protected_ip && is_string($this->protected_ip)) {
            $this->protected_ip = json_decode($this->protected_ip, true);
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->protected_ip && is_array($this->protected_ip)) {
            $ipList = str_replace(["\r"], '', implode("\n", $this->protected_ip));
            $ipList = explode("\n", $ipList);
            $ipList = array_map('trim', $ipList);
            $this->protected_ip = json_encode($ipList);
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->protected_ip && is_string($this->protected_ip)) {
            $this->protected_ip = json_decode($this->protected_ip, true);
        }
    }

    /**
     * Возвращает баланс.
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }
}