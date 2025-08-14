<?php

/**
 * This is the model class for table "{{config_group}}".
 *
 * The followings are the available columns in table '{{config_group}}':
 * @property string $id
 * @property string $name
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property Config $config
 */
class ConfigGroup extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{config_group}}';
	}

    public function rules()
    {
        return array(
            array('name', 'required'),
            array('status', 'numerical', 'integerOnly'=>true),

            array('id, name, status', 'safe', 'on'=>'search'),
        );
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'config' => array(self::HAS_MANY, 'Config', 'group_id', 'order' => 'config.order'),
		);
	}

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'status',
        );
    }
}
