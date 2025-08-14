<?php

/**
 * This is the model class for table "{{gallery}}".
 *
 * The followings are the available columns in table '{{gallery}}':
 * @property string $id
 * @property string $img
 * @property integer $status
 * @property integer $sort
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ImageUploadBehavior $fileUpload
 */
class Gallery extends ActiveRecord
{
    const PATH_TO_FOLDER_WITH_IMAGE = 'images/gallery';



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{gallery}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('title, status, sort', 'filter', 'filter' => 'trim'),
			array('status, sort', 'required'),
			array('status, sort', 'numerical', 'integerOnly' => TRUE),
            array('img', 'file', 'types' => 'jpg,jpeg,png', 'allowEmpty' => TRUE),
            array('status', 'in', 'range' => array_keys($this->getStatusList())),
            array('title', 'length', 'max' => 255),
			array('id, img, status, sort, created_at, updated_at', 'safe', 'on' => 'search'),
		);
	}

    public function behaviors()
    {
        return array(
            'fileUpload' => array(
                'class'             => 'application.components.behaviors.ImageUploadBehavior',
                'uploadPath'        => '/' . app()->params['uploadPath'] . '/' . self::PATH_TO_FOLDER_WITH_IMAGE . '/',
                'imageNameCallback' => array($this, 'generateFileName'),
                'imgParams'         => array(
                    array(
                        'quality' => 70,
                        'width'   => config('gallery.big.width'),
                        'height'  => config('gallery.big.height'),
                        'thumb'   => array(
                            'quality' => 70,
                            'width'   => config('gallery.small.width'),
                            'height'  => config('gallery.small.height'),
                        ),
                    ),
                ),
            ),
        );
    }

    public function generateFileName()
    {
        return md5(time() . rand());
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'title'         => Yii::t('main', 'Название'),
			'img'           => Yii::t('main', 'Картинка'),
			'status'        => Yii::t('main', 'Статус'),
			'sort'          => Yii::t('main', 'Сортировка'),
			'created_at'    => Yii::t('main', 'Дата создания'),
			'updated_at'    => Yii::t('main', 'Дата обновления'),
		);
	}

    public function getStatusList()
    {
        $data = parent::getStatusList();
        unset($data[ActiveRecord::STATUS_DELETED]);

        return $data;
    }
}
