<?php

/**
 * This is the model class for table "{{user_messages}}".
 *
 * The followings are the available columns in table '{{user_messages}}':
 * @property string $id
 * @property string $user_id
 * @property string $message
 * @property integer $read
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class UserMessages extends ActiveRecord
{
    const STATUS_READ     = 1; // Прочитано
    const STATUS_NOT_READ = 0; // Не прочитано



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_messages}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('user_id, message', 'filter', 'filter' => 'trim'),
			array('user_id, message', 'required'),
			array('read, status', 'numerical', 'integerOnly'=>true),
			array('message', 'length', 'min' => 5),
			array('user_id', 'length', 'max' => 10),

			array('id, user_id, message, read, status, created_at, updated_at', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'user_id'       => Yii::t('main', 'Автор'),
			'message'       => Yii::t('main', 'Сообщение'),
			'read'          => Yii::t('main', 'Прочитано'),
			'status'        => Yii::t('main', 'Статус'),
			'created_at'    => Yii::t('main', 'Дата создания'),
			'updated_at'    => Yii::t('main', 'Дата обновления'),
		);
	}

    /**
     * Возвращает небольшую часть сообщения
     *
     * @param int $count_word (кол-во слов которые надо вернуть)
     *
     * @return string
     */
    public function getShortMessage($count_word = 10)
    {
        return wordLimiter(e(strip_tags($this->message)), $count_word, ' ...');
    }
}
