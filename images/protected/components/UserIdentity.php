<?php

Yii::import('application.modules.cabinet.models.UsersAuthLogs');

class UserIdentity extends CUserIdentity
{
    private $_id;

    const ERROR_STATUS_INACTIVE     = 3;
    const ERROR_STATUS_BANNED       = 4;
    const ERROR_STATUS_IP_NO_ACCESS = 5;


    /**
     * @var Users
     */
    private $_user;
    private $_ls_id;
    private $_gs_id;


    public function __construct($username, $password, $ls_id, $gs_id)
    {
        $this->username = $username;
        $this->password = $password;
        $this->_ls_id   = $ls_id;
        $this->_gs_id   = $gs_id;
    }

	public function authenticate()
	{
        $userIp      = userIp();
        $this->_user = Users::model()->with('profile')->find('login = :login AND ls_id = :ls_id', array(
            ':login' => $this->username,
            'ls_id'  => $this->_ls_id,
        ));

        if($this->_user === NULL)
        {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        elseif(Users::validatePassword($this->password, $this->_user->password) === FALSE)
        {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;

            // Сохраняю неудачную попытку входа
            db()->createCommand()->insert('{{users_auth_logs}}', array(
                'user_id'    => $this->_user->getPrimaryKey(),
                'ip'         => $userIp,
                'user_agent' => request()->getUserAgent(),
                'status'     => UsersAuthLogs::STATUS_AUTH_DENIED,
                'created_at' => date('Y-m-d H:i:s'),
            ));
        }
        elseif(!$this->_user->isActivated())
        {
            $this->errorCode = self::ERROR_STATUS_INACTIVE;
        }
        elseif($this->_user->isBanned())
        {
            $this->errorCode = self::ERROR_STATUS_BANNED;
        }
        elseif($this->_user->profile->protected_ip && !in_array($userIp, $this->_user->profile->protected_ip))
        {
            $this->errorCode = self::ERROR_STATUS_IP_NO_ACCESS;
        }
        else
        {
            $this->_id = $this->_user->getPrimaryKey();

            $this->_user->auth_hash = Users::generateAuthHash();

            $this->setState('auth_hash', $this->_user->auth_hash);
            $this->setState('gs_id', $this->_gs_id);
            $this->setState('ls_id', $this->_user->getLsId());

            $this->_user->save(FALSE, array('auth_hash', 'updated_at'));

            // Запись в лог
            db()->createCommand()->insert('{{users_auth_logs}}', array(
                'user_id'    => $this->_user->getPrimaryKey(),
                'ip'         => $userIp,
                'user_agent' => request()->getUserAgent(),
                'status'     => UsersAuthLogs::STATUS_AUTH_SUCCESS,
                'created_at' => date('Y-m-d H:i:s'),
            ));

            $this->errorCode = self::ERROR_NONE;
        }

        return !$this->errorCode;
	}

    public function getName()
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->_id;
    }
}
