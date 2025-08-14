<?php
namespace app\modules\backend\models;

use yii\base\Model;
use app\models\Bonuses;
use app\models\BonusCodes;
use app\models\UserBonuses;
use app\models\BonusCodesActivatedLogs;
use app\models\Users;
use Yii;

class BonusActivateForm extends Model
{
    public $login;
    public $code;

    public function rules()
    {
        return [
            [['login', 'code'], 'required'],
            [['login'], 'string', 'max' => 54],
            [['code'], 'string', 'max' => 128],
        ];
    }

    public function activate()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = Users::findOne(['login' => $this->login]);
        if (!$user) {
            $this->addError('login', 'Пользователь не найден.');
            return false;
        }

        $bonusCode = BonusCodes::findOne(['code' => $this->code, 'status' => 1]);
        if (!$bonusCode) {
            $this->addError('code', 'Бонус код не найден или неактивен.');
            return false;
        }

        $activationsCount = BonusCodesActivatedLogs::find()->where(['code_id' => $bonusCode->id])->count();
        if ($activationsCount >= $bonusCode->limit) {
            $this->addError('code', 'Достигнут лимит активаций для этого кода.');
            return false;
        }

        $alreadyActivated = BonusCodesActivatedLogs::find()->where(['code_id' => $bonusCode->id, 'user_id' => $user->user_id])->exists();
        if ($alreadyActivated) {
            $this->addError('code', 'Вы уже активировали этот бонус.');
            return false;
        }

        $userBonus = new UserBonuses();
        $userBonus->bonus_id = $bonusCode->bonus_id;
        $userBonus->user_id = $user->user_id;
        $userBonus->status = 1;
        $userBonus->created_at = date('Y-m-d H:i:s');
        if (!$userBonus->save()) {
            $this->addError('code', 'Не удалось выдать бонус.');
            return false;
        }

        $log = new BonusCodesActivatedLogs();
        $log->code_id = $bonusCode->id;
        $log->user_id = $user->user_id;
        $log->created_at = date('Y-m-d H:i:s');
        $log->save();

        return true;
    }
}
