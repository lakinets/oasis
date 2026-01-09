<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Services;

class RemoveKarmaForm extends Model
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
     * Снять карму персонажу.
     *
     * @param Services $service
     * @param \yii\db\Connection $gameDb
     * @param mixed $driver
     * @return bool
     */
    public function executeChange(Services $service, \yii\db\Connection $gameDb, $driver): bool
    {
        $transaction = $gameDb->beginTransaction();
        try {
            // 1. Проверка баланса
            $profile = \app\models\UserProfiles::findOne(['user_id' => Yii::$app->user->id]);
            if (!$profile || (float)$profile->balance < (float)$service->cost) {
                $this->addError('characterId', 'Недостаточно средств на балансе.');
                $transaction->rollBack();
                return false;
            }

            $charId = (int)$this->characterId;

            // 2. Имя таблицы и поля
            $tableName = 'characters';
            $idField   = $driver->getField('characters', 'id_field');
            if (!$idField || $idField == 'id_field') {
                $idField = 'obj_Id';
            }

            // 3. Проверяем карму
            $karma = (new \yii\db\Query())
                ->select(['karma'])
                ->from($tableName)
                ->where([$idField => $charId])
                ->scalar($gameDb);

            if ($karma === false) {
                $this->addError('characterId', 'Персонаж не найден.');
                $transaction->rollBack();
                return false;
            }

            if ((int)$karma <= 0) {
                $this->addError('characterId', 'У этого персонажа нет кармы (PK).');
                $transaction->rollBack();
                return false;
            }

            // 4. Обнуляем карму и PK-убийства
            $gameDb->createCommand()
                ->update($tableName, ['karma' => 0, 'pkkills' => 0], [$idField => $charId])
                ->execute();

            // 5. Списываем валюту
            $profile->balance = (float)$profile->balance - (float)$service->cost;
            if (!$profile->save(false)) {
                throw new \Exception('Не удалось сохранить баланс.');
            }

            // 6. Успех – просто завершаем транзакцию
            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Ошибка снятия кармы: " . $e->getMessage(), 'services');
            $this->addError('characterId', 'Произошла ошибка при обработке запроса.');
            return false;
        }
    }
}