<?php
namespace app\modules\register\models;

use Yii;
use yii\base\Model;
use yii\captcha\CaptchaValidator;
use app\models\User;
use app\models\Gs;
use app\components\AppConfig;
use app\modules\register\services\RegistrationService;

class RegisterForm extends Model
{
    public string $login = '';
    public string $password = '';
    public string $re_password = '';
    public string $email = '';
    public string $prefix = '';
    public string $referer = '';
    public string $verifyCode = '';   // <-- капча
    public int    $gs_id = 0;

    public array $gs_list = [];
    public ?User $refererInfo = null;
    private array $allowedPrefixes = [];

    public function init()
    {
        parent::init();
        $this->loadGsList();
        $this->loadPrefixes();
        $this->autoPickPrefixIfNeeded();
    }

    private function loadGsList(): void
    {
        $this->gs_list = Gs::find()->where(['status' => 1])->indexBy('id')->all();
        if (count($this->gs_list) === 1) {
            $this->gs_id = (int) array_key_first($this->gs_list);
        }
    }

    private function loadPrefixes(): void
    {
        $list = AppConfig::prefixList();
        if (empty($list)) {
            $len = max(1, AppConfig::prefixLength());
            $letters = 'abcdefghijklmnopqrstuvwxyz';
            $pool = [];
            for ($i = 0; $i < 50; $i++) {
                $s = '';
                for ($j = 0; $j < $len; $j++) $s .= $letters[random_int(0, strlen($letters) - 1)];
                $pool[] = $s;
            }
            $list = array_values(array_unique($pool));
        }
        $this->allowedPrefixes = $list;
    }

    private function autoPickPrefixIfNeeded(): void
    {
        if (!AppConfig::prefixesEnabled()) {
            $this->prefix = '';
            return;
        }
        if ($this->prefix === '' && !empty($this->allowedPrefixes)) {
            $this->prefix = $this->allowedPrefixes[array_rand($this->allowedPrefixes)];
        }
    }

    public function rules(): array
    {
        $rules = [
            [['gs_id', 'login', 'password', 're_password', 'email'], 'required'],
            ['email', 'email'],
            ['login', 'string', 'min' => 3, 'max' => 14],
            ['password', 'string', 'min' => 6],
            ['re_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
            ['login', 'match', 'pattern' => '/^[A-Za-z0-9-]+$/', 'message' => 'Недопустимые символы в логине'],
            ['email', 'validateEmailUnique'],
            ['login', 'validateLoginUnique'],
            ['gs_id', 'validateGsExists'],
        ];

        if (AppConfig::captchaEnabled()) {
            $rules[] = ['verifyCode', CaptchaValidator::class, 'captchaAction' => 'register/default/captcha'];
        }

        if (AppConfig::prefixesEnabled()) {
            $rules[] = [['prefix'], 'required'];
            $rules[] = [['prefix'], 'validatePrefixAllowed'];
        } else {
            $this->prefix = '';
        }

        if (AppConfig::referralsEnabled()) {
            $rules[] = [['referer'], 'string', 'min' => 6, 'max' => 10];
            $rules[] = [['referer'], 'validateReferer'];
        }

        return $rules;
    }

    public function attributeLabels(): array
    {
        return [
            'gs_id'       => 'Выбор сервера',
            'prefix'      => 'Префикс',
            'login'       => 'Игровой логин',
            'password'    => 'Пароль',
            're_password' => 'Повторите пароль',
            'email'       => 'Email',
            'referer'     => 'Реферал',
            'verifyCode'  => 'Код с картинки',
        ];
    }

    public function validateEmailUnique($attribute): void
    {
        if (!$this->gs_id || !isset($this->gs_list[$this->gs_id])) return;
        if (User::find()->where(['email' => $this->$attribute, 'ls_id' => $this->gs_list[$this->gs_id]->login_id])->exists()) {
            $this->addError($attribute, 'Email уже существует.');
        }
    }

    public function validateLoginUnique($attribute): void
    {
        if (!$this->gs_id || !isset($this->gs_list[$this->gs_id])) {
            $this->addError('gs_id', 'Выберите корректный сервер.');
            return;
        }
        $login = $this->getLogin();
        $lsId  = $this->gs_list[$this->gs_id]->login_id;

        if (User::find()->where(['login' => $login, 'ls_id' => $lsId])->exists()) {
            $this->addError($attribute, 'Логин уже существует.');
            return;
        }

        try {
            $svc = new RegistrationService();
            if ($svc->lsLoginExists($lsId, $login)) {
                $this->addError($attribute, 'Логин уже существует на сервере.');
            }
        } catch (\Throwable $e) {
            $this->addError($attribute, 'Ошибка подключения к login-серверу: ' . $e->getMessage());
        }
    }

    public function validatePrefixAllowed($attribute): void
    {
        if (!in_array($this->$attribute, $this->allowedPrefixes, true)) {
            $this->addError($attribute, 'Префикс недоступен. Обновите страницу и попробуйте снова.');
        }
    }

    public function validateReferer($attribute): void
    {
        if (!$this->$attribute) return;
        if (!$this->gs_id || !isset($this->gs_list[$this->gs_id])) return;

        $cookieName = Yii::$app->params['cookie_referer_name'] ?? 'ref';
        $cookieValue = Yii::$app->request->cookies->getValue($cookieName);
        if (!$this->$attribute && $cookieValue) {
            $this->$attribute = $cookieValue;
        }

        $lsId = $this->gs_list[$this->gs_id]->login_id;
        $this->refererInfo = User::findOne(['referer' => $this->$attribute, 'ls_id' => $lsId]);
        if (!$this->refererInfo) {
            $this->$attribute = '';
        }
    }

    public function validateGsExists($attribute): void
    {
        if (!isset($this->gs_list[$this->$attribute])) {
            $this->addError($attribute, 'Выберите корректный сервер.');
        }
    }

    public function getLogin(): string
    {
        return strtolower(($this->prefix ?: '') . $this->login);
    }

    public function registerAccount(): void
    {
        $login = $this->getLogin();
        $lsId  = $this->gs_list[$this->gs_id]->login_id;
        $svc   = new RegistrationService();

        if (AppConfig::emailConfirmEnabled()) {
            $svc->registerWithEmailConfirm($lsId, $login, $this->password, $this->email);
            Yii::$app->session->setFlash('success', 'На почту отправлены инструкции по активации.');
        } else {
            $svc->registerImmediate($lsId, $login, $this->password, $this->email, $this->refererInfo->id ?? null);
            Yii::$app->session->setFlash('success', 'Регистрация прошла успешно!');
        }
    }

    public function getAllowedPrefixes(): array
    {
        return $this->allowedPrefixes;
    }
}