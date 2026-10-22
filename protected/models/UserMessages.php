<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{user_messages}}".
 *
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
    const STATUS_READ     = 1;
    const STATUS_NOT_READ = 0;

    public static function tableName()
    {
        return '{{%user_messages}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'message'], 'filter', 'filter' => 'trim'],
            [['user_id', 'message'], 'required'],
            [['read', 'status'], 'integer'],
            [['message'], 'string', 'min' => 5],
            [['user_id'], 'string', 'max' => 10],
            [['id', 'user_id', 'message', 'read', 'status', 'created_at', 'updated_at'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'user_id'       => Yii::t('main', 'Автор'),
            'message'       => Yii::t('main', 'Сообщение'),
            'read'          => Yii::t('main', 'Прочитано'),
            'status'        => Yii::t('main', 'Статус'),
            'created_at'    => Yii::t('main', 'Дата создания'),
            'updated_at'    => Yii::t('main', 'Дата обновления'),
        ];
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
        // Аналог Yii1 wordLimiter + e()
        return StringHelper::truncateWords(Html::encode(strip_tags($this->message)), $count_word, ' ...');
    }
}