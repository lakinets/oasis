<?php
namespace app\modules\backend\models;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property int    $id
 * @property string $title
 * @property string $image
 * @property int    $sort
 * @property int    $status
 */
class Gallery extends ActiveRecord
{
    public $imageFile; // virtual attribute for upload

    public static function tableName()
    {
        return 'gallery';
    }

    public function rules()
    {
        return [
            [['title', 'sort', 'status'], 'required'],
            [['title', 'image'], 'string', 'max' => 255],
            [['sort', 'status'], 'integer'],
            ['imageFile', 'image', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // загрузка файла
        if ($this->imageFile) {
            $fileName = uniqid('gallery_') . '.' . $this->imageFile->extension;
            $path = Yii::getAlias('@webroot/uploads/gallery/') . $fileName;
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            $this->imageFile->saveAs($path);
            $this->image = $fileName;
        }
        return true;
    }

    public function getStatusLabel()
    {
        return $this->status ? 'Активен' : 'Не активен';
    }

    public function getImageUrl()
    {
        return $this->image ? '/uploads/gallery/' . $this->image : '/img/no-image.png';
    }
}