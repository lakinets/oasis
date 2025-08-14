<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Users extends ActiveRecord implements IdentityInterface
{
    public const ROLE_ADMIN = 1;
    public const ROLE_DEFAULT = 0;

    public static function tableName()
    {
        return 'users'; // замени на название таблицы в БД
    }

    public function rules(): array
    {
        return [
            [['login', 'email', 'role', 'activated'], 'required'],
            [['role', 'activated'], 'integer'],
            [['login', 'email'], 'string', 'max' => 255],
            // другие правила
        ];
    }

    public function getRoleLabel()
    {
        return $this->role == self::ROLE_ADMIN ? 'Админ' : 'Пользователь';
    }

    // Реализация методов IdentityInterface (findIdentity, getId, и т.п.)
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // если не используешь токены
    }
    public function getId()
    {
        return $this->primaryKey;
    }
    public function getAuthKey()
    {
        return null;
    }
    public function validateAuthKey($authKey)
    {
        return true;
    }
}
