<?php

/**
 * This is the model class for table "{{transactions}}".
 *
 * The followings are the available columns in table '{{transactions}}':
 * @property string $id
 * @property string $payment_system
 * @property string $user_id
 * @property integer $sum
 * @property integer $count
 * @property integer $status
 * @property string $params
 * @property integer $gs_id
 * @property string $updated_at
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property Users[] $users
 * @property Users $user
 */
class Transactions extends ActiveRecord
{
    // Status
    const STATUS_SUCCESS    = 1;
    const STATUS_FAILED     = 0;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{transactions}}';
    }

    public function rules()
    {
        return array(
            array('id, payment_system, sum, status, user_ip, course_payments', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'users' => array(self::BELONGS_TO, 'Users', 'user_id'),
            'user'  => array(self::HAS_ONE, 'Users', array('user_id' => 'user_id')),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'                => 'ID',
            'payment_system'    => Yii::t('backend', 'Платежная система'),
            'user_id'           => Yii::t('backend', 'Юзер'),
            'sum'               => Yii::t('backend', 'Кол-во'),
            'count'             => Yii::t('backend', 'Кол-во игровой валюты'),
            'status'            => Yii::t('backend', 'Статус'),
            'user_ip'           => Yii::t('backend', 'IP'),
            'created_at'        => Yii::t('backend', 'Дата создания'),
            'updated_at'        => Yii::t('backend', 'Дата обновления'),
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id, true);
        $criteria->compare('t.payment_system', $this->payment_system, true);
        $criteria->compare('t.user_id', $this->user_id, true);
        $criteria->compare('t.sum', $this->sum);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.created_at', $this->created_at, true);

        $criteria->with = array('user');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 't.status DESC, t.created_at DESC'
            ),
            'pagination' => array(
                'pageSize' => 20,
                'pageVar' => 'page',
            ),
        ));
    }

    public function getStatusList()
    {
        return array(
            self::STATUS_SUCCESS    => Yii::t('main', 'Оплачена'),
            self::STATUS_FAILED     => Yii::t('main', 'Не оплачена'),
        );
    }

    public function getStatus()
    {
        $data = $this->getStatusList();
        return isset($data[$this->status]) ? $data[$this->status] : Yii::t('backend', '*Unknown*');
    }

    public function getType()
    {
        Yii::import('application.modules.deposit.extensions.Deposit.Deposit');

        $data = Deposit::getAggregatorsList();
        return isset($data[$this->payment_system]) ? $data[$this->payment_system] : '*Unknown*';
    }

    public function getDate()
    {
        return date('Y-m-d H:i', strtotime($this->created_at));
    }

    /**
     * Оплачена ли транзакция
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status == self::STATUS_SUCCESS;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return int
     */
    public function getGsId()
    {
        return $this->gs_id;
    }

    /**
     * @return Users[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return Users
     */
    public function getUser()
    {
        return $this->user;
    }
}
