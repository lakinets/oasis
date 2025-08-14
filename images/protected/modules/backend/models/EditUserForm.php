<?php
namespace app\modules\backend\models;

use yii\base\Model;
use yii\validators\IpValidator;
use app\modules\backend\models\Users;
use app\modules\backend\models\UserProfiles;

class EditUserForm extends Model
{
    public $role;
    public $activated;
    public $vote_balance;
    public $balance;
    public $phone;
    public $protected_ip;

    public function rules()
    {
        return [
            [['role', 'activated', 'vote_balance', 'balance'], 'required'],
            ['role', 'in', 'range' => array_keys($this->getRoleList()), 'message' => Yii::t('backend', 'Выберите роль')],
            ['activated', 'in', 'range' => array_keys($this->getActivatedStatusList()), 'message' => Yii::t('backend', 'Выберите статус аккаунта')],
            [['vote_balance', 'balance'], 'number', 'message' => Yii::t('backend', 'Введите число')],
            ['protected_ip', IpValidator::class],
            ['phone', 'string', 'max' => 54],
        ];
    }

    public function attributeLabels()
    {
        return [
            'role'         => (new Users())->getAttributeLabel('role'),
            'activated'    => (new Users())->getAttributeLabel('activated'),
            'vote_balance' => (new UserProfiles())->getAttributeLabel('vote_balance'),
            'balance'      => (new UserProfiles())->getAttributeLabel('balance'),
            'phone'        => (new UserProfiles())->getAttributeLabel('phone'),
            'protected_ip' => (new UserProfiles())->getAttributeLabel('protected_ip'),
        ];
    }

    public function getRoleList()
    {
        return Users::getRoleList();
    }

    public function getActivatedStatusList()
    {
        return Users::getActivatedStatusList();
    }
}