<?php 
 
class WebAdmin extends CWebUser
{
    /**
     * @var Users
     */
    protected $_user;



    protected function beforeLogout()
    {
        $userId = $this->getId();
        $date   = date('Y-m-d H:i:s');

        db()->createCommand("UPDATE {{users}} SET auth_hash = NULL, updated_at = :updated_at WHERE user_id = :user_id LIMIT 1")
            ->bindParam('user_id', $userId, PDO::PARAM_INT)
            ->bindParam('updated_at', $date, PDO::PARAM_STR)
            ->execute();

        return parent::beforeLogout();
    }


    public function init()
    {
        parent::init();

        if($this->getIsGuest() === FALSE)
        {
            // Если юзер залогинен то обновляю его инфу
            $this->_user = Users::model()->with('profile')->find('auth_hash = :auth_hash AND role = :role', array(
                'auth_hash' => $this->getState('auth_hash'),
                'role'      => Users::ROLE_ADMIN,
            ));

            if(!$this->_user)
            {
                $this->logout();
            }
        }
    }

    /**
     * @return Users
     */
    public function getUser()
    {
        return $this->_user;
    }
}
 