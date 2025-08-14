<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\validators\IpValidator;

class EditUserForm extends Model
{
    public $user_id;
    public $role;
    public $activated;
    public $balance;
    public $phone;
    public $protected_ip;

    public function rules()
    {
        return [
            [['user_id', 'role', 'activated', 'balance'], 'required'],
            [['user_id', 'activated'], 'integer'],
            ['balance', 'number', 'min' => 0],
            ['role', 'in', 'range' => array_keys(static::getRoleList())],
            ['activated', 'in', 'range' => array_keys(static::getActivatedStatusList())],
            ['phone', 'string', 'max' => 54],
            ['protected_ip', 'validateIpList'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id'      => 'ID',
            'role'         => 'Роль',
            'activated'    => 'Активирован',
            'balance'      => 'Баланс',
            'phone'        => 'Телефон',
            'protected_ip' => 'Разрешённые IP',
        ];
    }

    public static function getRoleList()
    {
        return Users::getRoleList();
    }

    public static function getActivatedStatusList()
    {
        return Users::getActivatedStatusList();
    }

    public function validateIpList($attribute)
    {
        if (empty($this->$attribute)) return;

        $ips = preg_split('/\r\n|\r|\n/', $this->$attribute);
        $validator = new IpValidator(['ipv6' => false]);

        foreach ($ips as $ip) {
            $ip = trim($ip);
            if ($ip === '') continue;
            if (!$validator->validate($ip)) {
                $this->addError($attribute, Yii::t('backend', '{ip} — не верный IP адрес.', ['ip' => $ip]));
            }
        }
    }

    public function loadFromUser(Users $user)
    {
        $profile = $user->profile ?? new UserProfiles(['user_id' => $user->user_id]);

        $this->user_id      = $user->user_id;
        $this->role         = $user->role;
        $this->activated    = $user->activated;
        $this->balance      = (float)$profile->balance;
        $this->phone        = $profile->phone ?? '';
        $this->protected_ip = implode("\n", $profile->getProtectedIps());
    }

    public function applyToUser(Users $user)
    {
        $profile = $user->profile ?? new UserProfiles(['user_id' => $user->user_id]);

        $user->role      = $this->role;
        $user->activated = $this->activated;

        $profile->balance      = $this->balance;
        $profile->phone        = $this->phone;
        $profile->setProtectedIps(preg_split('/\r\n|\r|\n/', $this->protected_ip));

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($user->save() && $profile->save()) {
                $transaction->commit();
                return true;
            }
            $transaction->rollBack();
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}