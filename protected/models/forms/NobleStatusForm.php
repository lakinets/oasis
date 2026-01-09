<?php
namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\Services;

class NobleStatusForm extends Model
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

            // Проверяем, что статус ещё 0
            $nobless = (new \yii\db\Query())
                ->select(['nobless'])
                ->from($tableName)
                ->where([$idField => $charId])
                ->scalar($gameDb);

            if ($nobless === false || (int)$nobless !== 0) {
                $this->addError('characterId', 'Персонаж уже имеет статус дворянина.');
                $transaction->rollBack();
                return false;
            }

            // Устанавливаем статус 1
            $gameDb->createCommand()
                ->update($tableName, ['nobless' => 1], [$idField => $charId])
                ->execute();

            // Списываем валюту
            $profile->balance = (float)$profile->balance - (float)$service->cost;
            if (!$profile->save(false)) {
                throw new \Exception('Не удалось сохранить баланс.');
            }

            $transaction->commit();
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Ошибка покупки статуса дворянина: " . $e->getMessage(), 'services');
            $this->addError('characterId', 'Произошла ошибка при обработке запроса.');
            return false;
        }
    }
}