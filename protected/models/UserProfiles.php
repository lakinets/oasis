<?php

/**
 * This is the model class for table "{{user_profiles}}".
 *
 * The followings are the available columns in table '{{user_profiles}}':
 * @property int $id
 * @property int $user_id
 * @property int $balance
 * @property int $vote_balance
 * @property string $preferred_language
 * @property string $protected_ip
 * @property string $phone
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class UserProfiles extends ActiveRecord
{
    const DEFAULT_BALANCE      = 0; // Дефолтное значение баланса
    const DEFAULT_VOTE_BALANCE = 0; // Дефолтное значение баланса голосов



	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_profiles}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('balance', 'required'),
			array('balance', 'length', 'max'=>10),

            array('protected_ip', 'isValidIp', 'on' => 'security'),

			array('id, user_id, balance', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        return array(
            'user' => array(self::HAS_ONE, 'Users', array('user_id' => 'user_id')),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                 => 'ID',
			'user_id'            => Yii::t('main', 'Юзер'),
			'balance'            => Yii::t('main', 'Баланс'),
			'vote_balance'       => Yii::t('main', 'Голоса'),
			'preferred_language' => Yii::t('main', 'Предпочитаемый язык'),
			'protected_ip'       => Yii::t('main', 'IP адрес(а)'),
			'phone'              => Yii::t('main', 'Телефон'),
		);
	}

    /**
     * Проверка IP адреса(ов)
     */
    public function isValidIp()
    {
        if($this->protected_ip)
        {
            $ipList = explode("\r\n", $this->protected_ip);

            if($ipList)
            {
                foreach($ipList as $ip)
                {
                    if(!ip2long(trim($ip)))
                    {
                        $this->addError('protected_ip', Yii::t('main', ':bad_ip - не является верным IP адресом.', array(':bad_ip' => $ip)));
                    }
                }
            }
        }
    }

    protected function afterFind()
    {
        if($this->protected_ip)
        {
            $this->protected_ip = json_decode($this->protected_ip, TRUE);
        }

        parent::afterFind();
    }

    protected function beforeSave()
    {
        if($this->protected_ip)
        {
            $ipList = str_replace(array("\r"), '', $this->protected_ip);
            $ipList = explode("\n", $ipList);
            $ipList = array_map('trim', $ipList);

            $this->protected_ip = json_encode($ipList);
        }

        return parent::beforeSave();
    }

    protected function afterSave()
    {
        if($this->protected_ip)
        {
            $this->protected_ip = json_decode($this->protected_ip, TRUE);
        }

        parent::afterSave();
    }

    /**
     * Возвращает баланс
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }
}
