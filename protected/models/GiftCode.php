<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property int $nominal
 * @property float $cost
 * @property string $status
 * @property string $created_at
 * @property ?string $activated_at
 * @property ?int $activated_by
 */
class GiftCode extends ActiveRecord
{
    public static function tableName()
    {
        return 'gift_codes';
    }

    public function rules()
    {
        return [
            [['user_id', 'code', 'nominal'], 'required'],
            ['code', 'string', 'max' => 32],
            ['code', 'unique'],
            ['status', 'in', 'range' => ['active', 'activated']],
            [['activated_by'], 'integer'],
            [['activated_at', 'cost'], 'safe'],
        ];
    }

    public static function generateUniqueCode(): string
    {
        do {
            // Генерируем 16-значный читаемый код (A-Z, 0-9)
            $code = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 16));
            // Или альтернативный вариант:
            // $code = Yii::$app->security->generateRandomString(16);
        } while (self::find()->where(['code' => $code])->exists());

        return $code;
    }

    public static function isBlocked(int $userId): bool
    {
        $attempts = GiftCodeAttempt::find()
            ->where(['user_id' => $userId])
            ->andWhere(['>', 'attempt_at', date('Y-m-d H:i:s', strtotime('-72 hours'))])
            ->count();
        return $attempts >= 5;
    }

    public static function logAttempt(int $userId): void
    {
        $model = new GiftCodeAttempt();
        $model->user_id = $userId;
        $model->ip = Yii::$app->request->userIP;
        $model->save(false);
    }

    public static function clearAttempts(int $userId): void
    {
        GiftCodeAttempt::deleteAll(['user_id' => $userId]);
    }
}