<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Services;

class ChangeCharGenderForm extends Model
{
    public $characterId;

    public function rules()
    {
        return [
            ['characterId', 'required'],
            ['characterId', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'characterId' => 'Персонаж',
        ];
    }

    /**
     * Возвращает противоположный пол (0 → 1, 1 → 0)
     */
    public function getOppositeGender(int $current): int
    {
        return $current === 0 ? 1 : 0;
    }

    public function executeChange(Services $service, \yii\db\Connection $gameDb, $driver): bool
    {
        $transaction = $gameDb->beginTransaction();
        try {
            $profile = \app\models\UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
            if (!$profile || (float)$profile->balance < (float)$service->cost) {
                $this->addError('characterId', 'Недостаточно средств на балансе.');
                $transaction->rollBack();
                return false;
            }

            $charId = (int)$this->characterId;

            $tableName = 'characters';
            $idField   = $driver->getField('characters', 'id_field');
            if (!$idField || $idField == 'id_field') {
                $idField = 'obj_Id';
            }

            // Проверяем, что раса ≠ 7 (Arteia)
            $race = (new \yii\db\Query())
                ->select(['race'])
                ->from($tableName)
                ->where([$idField => $charId])
                ->scalar($gameDb);

            if ((int)$race === 7) {
                $this->addError('characterId', 'Смена пола недоступна для расы Arteia (ID 7).');
                $transaction->rollBack();
                return false;
            }

            // Получаем текущий пол
            $currentSex = (new \yii\db\Query())
                ->select(['sex'])
                ->from($tableName)
                ->where([$idField => $charId])
                ->scalar($gameDb);

            if ($currentSex === false) {
                $this->addError('characterId', 'Персонаж не найден.');
                $transaction->rollBack();
                return false;
            }

            $newSex = $this->getOppositeGender((int)$currentSex);

            // Обновляем пол
            $gameDb->createCommand()
                ->update($tableName, ['sex' => $newSex], [$idField => $charId])
                ->execute();

            // Списываем валюту
            $profile->balance = (float)$profile->balance - (float)$service->cost;
            if (!$profile->save(false)) {
                throw new \Exception('Не удалось сохранить баланс.');
            }

            // Сообщение для flash
            $old = $currentSex == 0 ? 'мужской' : 'женский';
            $new = $newSex == 0 ? 'мужской' : 'женский';
            Yii::$app->session->setFlash('success', "Пол изменён: был $old → стал $new.");

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Ошибка смены пола: " . $e->getMessage(), 'services');
            $this->addError('characterId', 'Произошла ошибка при обработке запроса.');
            return false;
        }
    }
}