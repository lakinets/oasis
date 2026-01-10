<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Services;
use app\models\GiftCode;
use app\models\UserProfiles;

class GiftCodeCreateForm extends Model
{
    public $nominal;

    public function rules()
    {
        return [
            ['nominal', 'required'],
            ['nominal', 'integer', 'min' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nominal' => 'Номинал кода (Web Aden)',
        ];
    }

    public function createCode(Services $service, array $allowedNominals): bool
    {
        if (!in_array((int)$this->nominal, $allowedNominals, true)) {
            $this->addError('nominal', 'Недопустимый номинал.');
            return false;
        }

        $userId  = Yii::$app->user->id;
        
        // Используем транзакцию для безопасности
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $profile = UserProfiles::findOne(['user_id' => $userId]);
            
            // Расчет: Номинал (сколько будет в коде) + Стоимость услуги (комиссия сайта)
            $totalCost = (float)$this->nominal + (float)$service->cost;

            if (!$profile || (float)$profile->balance < $totalCost) {
                $this->addError('nominal', "Недостаточно средств. Нужно: $totalCost (Номинал $this->nominal + Комиссия $service->cost).");
                $transaction->rollBack();
                return false;
            }

            // Создаем код
            $codeModel = new GiftCode();
            $codeModel->user_id    = $userId;
            $codeModel->code       = GiftCode::generateUniqueCode();
            $codeModel->nominal    = $this->nominal;
            $codeModel->cost       = $service->cost;
            $codeModel->status     = 'active';
            $codeModel->created_at = date('Y-m-d H:i:s');

            if (!$codeModel->save()) {
                throw new \Exception('Ошибка сохранения кода.');
            }

            // Списываем средства
            $profile->balance = (float)$profile->balance - $totalCost;
            if (!$profile->save(false)) {
                throw new \Exception('Ошибка списания средств.');
            }

            $transaction->commit();

            Yii::$app->session->setFlash(
                'success',
                "Код успешно создан: <b>{$codeModel->code}</b><br>" .
                "Списано: $totalCost Web Aden (Комиссия: {$service->cost})."
            );
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('nominal', 'Ошибка: ' . $e->getMessage());
            return false;
        }
    }
}