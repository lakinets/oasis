<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * Модель транзакций (таблица `transactions`)
 *
 * @property int $id
 * @property string $payment_system
 * @property int $user_id
 * @property int $sum
 * @property int $count
 * @property int $status
 * @property string $params
 * @property int $gs_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Transactions extends ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED  = 0;

    public static function tableName()
    {
        return '{{%transactions}}';
    }

    public function rules()
    {
        return [
            [['payment_system', 'user_id', 'sum', 'count', 'status', 'user_ip', 'created_at'], 'required'],
            [['user_id', 'sum', 'count', 'status', 'gs_id'], 'integer'],
            [['payment_system', 'user_ip'], 'string', 'max' => 255],
            [['params'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'payment_system' => 'Платежная система',
            'user_id'        => 'Юзер',
            'sum'            => 'Кол-во',
            'count'          => 'Игровая валюта',
            'status'         => 'Статус',
            'user_ip'        => 'IP',
            'created_at'     => 'Дата создания',
            'updated_at'     => 'Дата обновления',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(\app\models\User::class, ['user_id' => 'user_id']);
    }

    public function getStatusLabel()
    {
        return [
            self::STATUS_SUCCESS => 'Оплачена',
            self::STATUS_FAILED  => 'Не оплачена',
        ][$this->status] ?? 'Неизвестно';
    }

    public function getDate()
    {
        return \Yii::$app->formatter->asDatetime($this->created_at);
    }

    public function isPaid()
    {
        return $this->status == self::STATUS_SUCCESS;
    }

    public function search()
    {
        $query = self::find()->with('user')
            ->where(['user_id' => \Yii::$app->user->id])
            ->orderBy(['status' => SORT_DESC, 'created_at' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['cabinet.transaction_history.limit'] ?? 20,
                'pageVar' => 'page',
            ],
        ]);
    }
}