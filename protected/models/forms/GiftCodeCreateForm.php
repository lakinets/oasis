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
            'nominal' => Yii::t('views', 'Номинал кода (Web Aden)'),
        ];
    }

    public function createCode(Services $service, array $allowedNominals): bool
    {
        if (!in_array((int)$this->nominal, $allowedNominals, true)) {
            $this->addError('nominal', Yii::t('views', 'Недопустимый номинал.'));
            return false;
        }

        $userId = Yii::$app->user->id;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $profile = UserProfiles::findOne(['user_id' => $userId]);
            $totalCost = (float)$this->nominal + (float)$service->cost;

            if (!$profile) {
                $msg = Yii::t('views', 'Профиль пользователя не найден.');
                $this->addError('nominal', $msg);
                Yii::$app->session->setFlash('error', $msg);
                $transaction->rollBack();
                return false;
            }

            $balance = (float)$profile->balance;

            if ($balance < $totalCost) {
                $shortage = $totalCost - $balance;
                $msg = Yii::t(
                    'views',
                    'Недостаточно средств на балансе. Нужно {total} Web Aden (номинал {nominal} + комиссия {cost}), у вас {balance}. Не хватает {shortage}. Пополните баланс.',
                    [
                        'total'    => number_format($totalCost, 0, '.', ' '),
                        'nominal'  => number_format((float)$this->nominal, 0, '.', ' '),
                        'cost'     => number_format((float)$service->cost, 0, '.', ' '),
                        'balance'  => number_format($balance, 0, '.', ' '),
                        'shortage' => number_format($shortage, 0, '.', ' '),
                    ]
                );
                $this->addError('nominal', $msg);
                Yii::$app->session->setFlash('error', $msg);
                $transaction->rollBack();
                return false;
            }

            $codeModel = new GiftCode();
            $codeModel->user_id    = $userId;
            $codeModel->code       = GiftCode::generateUniqueCode();
            $codeModel->nominal    = $this->nominal;
            $codeModel->cost       = $service->cost;
            $codeModel->status     = 'active';
            $codeModel->created_at = date('Y-m-d H:i:s');

            if (!$codeModel->save()) {
                throw new \Exception(Yii::t('views', 'Ошибка сохранения кода.'));
            }

            $profile->balance = $balance - $totalCost;
            if (!$profile->save(false)) {
                throw new \Exception(Yii::t('views', 'Ошибка списания средств.'));
            }

            $transaction->commit();

            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'views',
                    'Код успешно создан: <b>{code}</b><br>Списано: {total} Web Aden (включая комиссию {cost}).',
                    [
                        'code'  => $codeModel->code,
                        'total' => number_format($totalCost, 0, '.', ' '),
                        'cost'  => number_format((float)$service->cost, 0, '.', ' '),
                    ]
                )
            );

            return true;

        } catch (\Exception $e) {
            if ($transaction->getIsActive()) {
                $transaction->rollBack();
            }
            $msg = Yii::t('views', 'Ошибка: {error}', ['error' => $e->getMessage()]);
            $this->addError('nominal', $msg);
            Yii::$app->session->setFlash('error', $msg);
            return false;
        }
    }
}