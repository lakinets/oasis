<?php
namespace app\modules\register\services;

use Yii;
use yii\db\Connection;
use app\models\User;
use app\models\Ls;
use app\components\AppConfig;

class RegistrationService
{
    /* =====================  Внутренняя фабрика  ===================== */

    /**
     * Возвращает подключение к БД конкретного login-сервера.
     */
    private function getDb(int $lsId): Connection
    {
        $ls = Ls::findOne($lsId);
        if (!$ls) {
            throw new \RuntimeException("Login-сервер id=$lsId не найден.");
        }

        return new Connection([
            'dsn'      => "mysql:host={$ls->db_host};port={$ls->db_port};dbname={$ls->db_name}",
            'username' => $ls->db_user,
            'password' => $ls->db_pass,
            'charset'  => 'utf8mb4',
        ]);
    }

    /**
     * Возвращает экземпляр обработчика (Lucera2, Acis_it и т.д.).
     */
    private function getHandler(int $lsId): object
    {
        $ls = Ls::findOne($lsId);
        $className = 'app\\l2j\\' . $ls->version;
        if (!class_exists($className)) {
            throw new \RuntimeException("Класс $className не найден в l2j/");
        }
        return new $className($this->getDb($lsId));
    }

    /**
     * Хеширует пароль согласно ls.password_type.
     */
    private function hashPassword(string $plain, int $lsId): string
    {
        $ls = Ls::findOne($lsId);
        return match ($ls->password_type ?? 'sha1') {
            'md5'    => md5($plain),
            'sha1'   => sha1($plain),
            'bcrypt' => password_hash($plain, PASSWORD_BCRYPT),
            default  => sha1($plain),
        };
    }

    /* =====================  Работа с login-сервером  ===================== */

    /**
     * Есть ли такой логин в accounts.
     */
    public function lsLoginExists(int $lsId, string $login): bool
    {
        $handler = $this->getHandler($lsId);

        // если у класса есть метод loginExists — используем
        if (method_exists($handler, 'loginExists')) {
            return $handler->loginExists($login);
        }

        // fallback: универсальный запрос
        return (bool) $this->getDb($lsId)
            ->createCommand('SELECT COUNT(*) FROM accounts WHERE login = :login')
            ->bindValue(':login', $login)
            ->queryScalar();
    }

    /**
     * Создаёт аккаунт в accounts.
     */
    public function createLsAccount(int $lsId, string $login, string $plainPassword): void
    {
        $handler = $this->getHandler($lsId);

        // если у класса есть метод createAccount — используем
        if (method_exists($handler, 'createAccount')) {
            $handler->createAccount($login, $plainPassword);
            return;
        }

        // fallback: универсальная вставка
        $this->getDb($lsId)->createCommand()->insert('accounts', [
            'login'    => $login,
            'password' => $this->hashPassword($plainPassword, $lsId),
        ])->execute();
    }

    /**
     * Удаляет аккаунт из accounts (rollback).
     */
    public function deleteLsAccountIfExists(int $lsId, string $login): void
    {
        $this->getDb($lsId)->createCommand()
            ->delete('accounts', ['login' => $login])
            ->execute();
    }

    /* =====================  Сайт  ===================== */

    /**
     * Создаёт пользователя в сайтовой БД.
     */
    public function createSiteUser(int $lsId, string $login, string $plainPassword, string $email, int $activated): User
    {
        $user = new User();
        $user->login     = $login;
        $user->setPassword($plainPassword); // использует bcrypt для сайта
        $user->email     = $email;
        $user->activated = $activated;
        $user->role      = 'user';
        $user->ls_id     = $lsId;
		$user->created_at = date('Y-m-d H:i:s'); 

        if (!$user->save()) {
            throw new \RuntimeException('Не удалось сохранить пользователя: ' .
                json_encode($user->getErrors(), JSON_UNESCAPED_UNICODE));
        }
        return $user;
    }

    /* =====================  Регистрация  ===================== */

    /**
     * Регистрация без email-подтверждения.
     */
    public function registerImmediate(int $lsId, string $login, string $password, string $email): void
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            $this->createLsAccount($lsId, $login, $password);
            $this->createSiteUser($lsId, $login, $password, $email, 1);
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            $this->deleteLsAccountIfExists($lsId, $login);
            throw $e;
        }
    }

    /**
     * Регистрация с email-подтверждением.
     */
    public function registerWithEmailConfirm(int $lsId, string $login, string $password, string $email): void
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            $user = $this->createSiteUser($lsId, $login, $password, $email, 0);

            $hash = Yii::$app->security->generateRandomString();
            Yii::$app->cache->set(
                'registerActivated' . $hash,
                ['user_id' => $user->user_id, 'password' => $password, 'email' => $email],
                AppConfig::emailConfirmTTLMinutes() * 60
            );

            Yii::$app->mailer->compose('@app/mail/register/step1', ['hash' => $hash])
                ->setTo($email)
                ->setSubject('Активация аккаунта')
                ->send();

            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        }
    }
}