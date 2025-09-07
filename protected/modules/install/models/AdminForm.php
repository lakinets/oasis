<?php
namespace app\modules\install\models;

use Yii;
use yii\base\Model;
use app\models\User;

class AdminForm extends Model
{
    public string $login = '';
    public string $password = '';
    public string $email = '';

    public function rules()
    {
        return [
            [['login', 'password', 'email'], 'required'],
            ['email', 'email'],
            ['password', 'string', 'min' => 6],
            // убираем unique — пользователей ещё нет
        ];
    }

	public function createAdmin(): void
	{
		$user = new User();
		$user->login = $this->login;
		$user->email = $this->email;
		$user->setPassword($this->password);
		$user->activated = 1;          // активен
		$user->role = 'admin';         // администратор
		$user->ls_id = 1;              // можно привязать к первому логин-серверу
		$user->created_at = date('Y-m-d H:i:s');
		$user->save();

		$auth = Yii::$app->authManager;
		if ($auth && !$auth->getRole('admin')) {
			$role = $auth->createRole('admin');
			$auth->add($role);
		}
		if ($auth) $auth->assign('admin', $user->id);
	}
}