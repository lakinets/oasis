<?php

/**
 * This is the model class for table "{{purchase_items_log}}".
 *
 * The followings are the available columns in table '{{purchase_items_log}}':
 * @property integer $id
 * @property integer $pack_id
 * @property integer $item_id
 * @property string $description
 * @property integer $cost
 * @property double $discount
 * @property string $currency_type
 * @property integer $count
 * @property integer $enchant
 * @property integer $user_id
 * @property integer $char_id
 * @property integer $gs_id
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property AllItems $itemInfo
 * @property Gs $gs
 */
class PurchaseItemsLog extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{purchase_items_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('pack_id, item_id, cost, enchant, user_id, char_id, created_at', 'required'),
			array('enchant', 'numerical', 'integerOnly'=>true),
			array('discount', 'numerical'),
			array('pack_id, item_id, cost, count, user_id, char_id', 'length', 'max'=>10),
			array('currency_type', 'length', 'max'=>54),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pack_id, item_id, description, cost, discount, currency_type, count, enchant, user_id, char_id, created_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'itemInfo' => array(self::HAS_ONE, 'AllItems', array('item_id' => 'item_id')),
            'gs' => array(self::HAS_ONE, 'Gs', array('id' => 'gs_id')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'pack_id'       => Yii::t('main', 'ID набора'),
			'item_id'       => Yii::t('main', 'ID предмета'),
			'description'   => Yii::t('main', 'Описание'),
			'cost'          => Yii::t('main', 'Стоимость'),
			'discount'      => Yii::t('main', 'Скидка на товар'),
			'currency_type' => Yii::t('main', 'Тип оплаты'),
			'count'         => Yii::t('main', 'Кол-во'),
			'enchant'       => Yii::t('main', 'Заточка'),
			'user_id'       => Yii::t('main', 'Кто купил'),
			'char_id'       => Yii::t('main', 'ID персонажа на сервере'),
			'created_at'    => 'Created At',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		/*$criteria->compare('id',$this->id,true);
		$criteria->compare('pack_id',$this->pack_id,true);
		$criteria->compare('item_id',$this->item_id,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('currency_type',$this->currency_type,true);
		$criteria->compare('count',$this->count,true);
		$criteria->compare('enchant',$this->enchant);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('char_id',$this->char_id,true);
		$criteria->compare('created_at',$this->created_at,true);*/

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
