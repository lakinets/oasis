<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\GiftCode;
use app\models\UserProfiles;

class GiftCodeActivateForm extends Model
{
    public $code;

    public function rules()
    {
        return [
            ['code', 'required'],
            ['code', 'string', 'max' => 32],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'Подарочный код',
        ];
    }

    public function activateCode(): bool
    {
        $userId = Yii::$app->user->id;

        // 1. Проверка на brute-force
        if (GiftCode::isBlocked($userId)) {
            $this->addError('code', 'Вы заблокированы на 72 часа за перебор кодов.');
            return false;
        }

        $code = trim($this->code); // Регистр важен, если в генераторе A-Z, то и тут ищем A-Z

        // 2. Поиск кода
        $model = GiftCode::find()
            ->where(['code' => $code, 'status' => 'active'])
            ->one();

        if (!$model) {
            GiftCode::logAttempt($userId);
            $this->addError('code', 'Код не найден или уже активирован.');
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 3. Активация кода
            $model->status       = 'activated';
            $model->activated_at = date('Y-m-d H:i:s');
            $model->activated_by = $userId;
            
            if (!$model->save(false)) {
                throw new \Exception('Ошибка обновления статуса кода.');
            }

            // 4. Начисление баланса
            $profile = UserProfiles::findOne(['user_id' => $userId]);
            if ($profile) {
                $profile->balance = (float)$profile->balance + (float)$model->nominal;
                if (!$profile->save(false)) {
                    throw new \Exception('Ошибка обновления баланса.');
                }
            }

            // Сброс попыток при успехе
            GiftCode::clearAttempts($userId);

            $transaction->commit();

            Yii::$app->session->setFlash(
                'success',
                "Код активирован! На ваш баланс зачислено: <b>{$model->nominal}</b> Web Aden."
            );
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('code', 'Ошибка активации: ' . $e->getMessage());
            return false;
        }
    }
}