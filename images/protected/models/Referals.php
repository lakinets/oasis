<?php

/**
 * This is the model class for table "{{referals}}".
 *
 * The followings are the available columns in table '{{referals}}':
 * @property string $referer
 * @property string $referal
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property Users $referalInfo
 */
class Referals extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{referals}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('referer, referal, created_at', 'required'),
			array('referer, referal', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('referer, referal, created_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'referalInfo' => array(self::HAS_ONE, 'Users', array('user_id' => 'referal')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'referer'       => 'ID кто пригласил',
			'referal'       => 'ID кого пригласили',
			'created_at'    => 'Created At',
		);
	}
}
