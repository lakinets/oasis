<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{user_actions_log}}".
 *
 * @property string $id
 * @property string $user_id
 * @property integer $action_id
 * @property string $params
 * @property string $created_at
 */
class UserActionsLog extends ActiveRecord
{
    const ACTION_AUTH = 1;
    const ACTION_DEPOSIT_SUCCESS = 2;
    const ACTION_CHANGE_PASSWORD = 3;
    const ACTION_CREATE_TICKET = 4;
    const ACTION_TELEPORT_TO_TOWN = 5;
    const ACTION_ACTIVATED_BONUS = 6;
    const ACTION_ACTIVATED_BONUS_CODE = 7;
    const ACTION_SHOP_BUY_ITEM = 8;
    const ACTION_SERVICES_BUY_PREMIUM = 9;
    const ACTION_SERVICES_REMOVE_HWID = 10;

    public static function tableName()
    {
        return '{{%user_actions_log}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'action_id', 'created_at'], 'required'],
            [['action_id'], 'integer'],
            [['user_id'], 'string', 'max' => 10],
            [['id', 'user_id', 'action_id', 'created_at'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('models', 'Пользователь'),
            'action_id' => Yii::t('models', 'Действие'),
            'created_at' => Yii::t('models', 'Дата создания'),
        ];
    }

    public function search()
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load(Yii::$app->request->queryParams);
        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'user_id', $this->user_id])
              ->andFilterWhere(['action_id' => $this->action_id])
              ->andFilterWhere(['like', 'created_at', $this->created_at]);

        return $dataProvider;
    }
}