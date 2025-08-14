<?php

/**
 * This is the model class for table "{{user_actions_log}}".
 *
 * The followings are the available columns in table '{{user_actions_log}}':
 * @property string $id
 * @property string $user_id
 * @property integer $action_id
 * @property string $params
 * @property string $created_at
 */
class UserActionsLog extends ActiveRecord
{
    /**
     * Авторизация
     */
    const ACTION_AUTH = 1;

    /**
     * Пополнение баланса
     */
    const ACTION_DEPOSIT_SUCCESS = 2;

    /**
     * Смена пароля
     */
    const ACTION_CHANGE_PASSWORD = 3;

    /**
     * Создание тикета
     */
    const ACTION_CREATE_TICKET = 4;

    /**
     * Телепорт персонажа в город
     */
    const ACTION_TELEPORT_TO_TOWN = 5;

    /**
     * Активация бонуса
     */
    const ACTION_ACTIVATED_BONUS = 6;

    /**
     * Активация бонус кода
     */
    const ACTION_ACTIVATED_BONUS_CODE = 7;

    /**
     * Покупка предмета в магазине
     */
    const ACTION_SHOP_BUY_ITEM = 8;

    /**
     * Покупка ПА
     */
    const ACTION_SERVICES_BUY_PREMIUM = 9;

    /**
     * Удаление HWID
     */
    const ACTION_SERVICES_REMOVE_HWID = 10;



    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_actions_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, action_id, created_at', 'required'),
			array('action_id', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, action_id, created_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'action_id' => 'ID того что сделал юзер',
			'created_at' => 'Created At',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('action_id',$this->action_id);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
