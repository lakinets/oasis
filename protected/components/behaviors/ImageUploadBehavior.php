<?php 
 
class ImageUploadBehavior extends CActiveRecordBehavior
{
    /**
     * Аттрибут модели для хранения картинки
     * @var string
     */
    public $attributeName = 'img';

    /**
     * Директория для загрузки картинки
     * @var string
     */
    public $uploadPath = 'uploads';

    /**
     * Список сценариев в которых будет использовано поведение
     * @var array
     */
    public $scenarios = array('insert', 'update');

    public $imgParams = array(
        array(
            'quality' => 80,
            'width'   => 800,
            'height'  => 800,
            'thumb'   => array(
                'quality' => 80,
                'width'   => 150,
                'height'  => 150,
            ),
        ),
    );

    public $defaultQuality = 80;

    /**
     * Callback функция для генерации имени загружаемого файла
     * @var
     */
    public $imageNameCallback;


    protected $_new_image = array();
    protected $_old_image;
    protected $_image_path;



    /*public function attach($owner)
    {
        parent::attach($owner);

        if($this->checkScenario())
        {
            // добавляем валидатор файла
            $fileValidator = CValidator::createValidator('file', $owner, $this->attributeName, array(
                'types'      => $this->types,
                'minSize'    => $this->min_size,
                'maxSize'    => $this->max_size,
                'allowEmpty' => TRUE,
            ));

            $owner->validatorList->add($fileValidator);
        }
    }*/

    public function afterValidate($event)
    {
        parent::afterValidate($event);

        if($this->checkScenario() && !$this->owner->hasErrors())
        {
            $modelName = get_class($this->owner);

            if(isset($_FILES[$modelName]['name'][$this->attributeName]))
            {
                $files = array();

                foreach($_FILES[$modelName] as $k => $v)
                {
                    $files[$k] = $v[$this->attributeName];
                }

                // Сохраняю
                if($files)
                {
                    Yii::import('ext.ImageUpload.ImageUpload');

                    $handle = new ImageUpload($files);

                    if($handle->uploaded)
                    {
                        $this->saveImage($handle);

                        // Удаляю старую фотку
                        if($this->owner->scenario == 'update')
                        {
                            $this->deleteImage();
                        }

                        $handle->clean();
                    }

                    unset($handle, $files);
                }
            }
        }
    }

    public function afterFind($event)
    {
        $this->_old_image = $this->owner->{$this->attributeName};
    }

    /**
     * Возвращает путь до папки с картинкой
     *
     * @return string
     */
    public function getImgPath()
    {
        return trim($this->uploadPath, '/') . '/';
    }

    /**
     * Возвращает URL до картинки
     *
     * @return string
     */
    public function getImgUrl()
    {
        return app()->createAbsoluteUrl($this->uploadPath) . $this->owner->{$this->attributeName};
    }

    public function getThumbUrl()
    {
        return app()->createAbsoluteUrl($this->uploadPath) . $this->getThumbName();
    }

    /**
     * Проверяет, есть ли картинка на месте
     *
     * @return bool
     */
    public function imgIsExists()
    {
        return is_file(Yii::getPathOfAlias('webroot') . '/' . $this->getImgPath() . $this->owner->{$this->attributeName});
    }

    /**
     * Информация о картинки
     *
     * @return array
     */
    public function getImgInfo()
    {
        if($this->imgIsExists())
        {
            return getimagesize($this->getImgPath());
        }

        return FALSE;
    }


    /**
     * Возвращает название файла превъюшки
     *
     * @param string $img
     *
     * @return mixed
     */
    private function getThumbName($img = NULL)
    {
        if($img === NULL)
        {
            $img = $this->owner->{$this->attributeName};
        }

        $info = pathinfo($this->getImgPath() . $img);

        if(!isset($info['extension']))
        {
            return '';
        }

        return $info['filename'] . '_thumb.' . $info['extension'];
    }


    public function checkScenario()
    {
        return in_array($this->owner->scenario, $this->scenarios);
    }

    protected function _getImageName()
    {
        return is_callable($this->imageNameCallback)
            ? call_user_func($this->imageNameCallback)
            : md5(microtime(TRUE) . rand() . rand());
    }

    public function saveImage(ImageUpload $image)
    {
        $path = Yii::getPathOfAlias('webroot') . '/' . $this->getImgPath();

        foreach($this->imgParams as $params)
        {
            $width      = (int) $params['width'];
            $height     = (int) $params['height'];
            $quality    = isset($params['quality']) ? (int) $params['quality'] : $this->defaultQuality;
            $imageName  = $this->_getImageName();

            if($image->processed)
            {
                $image->image_resize        = TRUE;
                $image->file_new_name_body  = $imageName;
                $image->image_x             = $width;
                $image->image_y             = $height;
                $image->image_ratio         = TRUE;
                $image->jpeg_quality        = $quality;

                $image->process($path);

                if($image->processed)
                {
                    $this->owner->{$this->attributeName} = $image->file_dst_name;

                    if(isset($params['thumb']))
                    {
                        $width      = (int) $params['thumb']['width'];
                        $height     = (int) $params['thumb']['height'];
                        $quality    = isset($params['thumb']['quality']) ? (int) $params['thumb']['quality'] : $this->defaultQuality;

                        $image->image_resize        = TRUE;
                        $image->file_new_name_body  = $imageName;
                        $image->file_name_body_add  = '_thumb';
                        $image->image_x             = $width;
                        $image->image_y             = $height;
                        $image->jpeg_quality        = $quality;
                        $image->image_ratio         = TRUE;

                        $image->process($path);

                        if(!$image->processed)
                        {
                            Yii::log($image->error, CLogger::LEVEL_ERROR, 'ImageUploadBehavior');
                        }
                    }
                }
                else
                {
                    Yii::log($image->error, CLogger::LEVEL_ERROR, 'ImageUploadBehavior');
                }
            }
            else
            {
                Yii::log($image->error, CLogger::LEVEL_ERROR, 'ImageUploadBehavior');
            }
        }
    }

    public function afterDelete($event)
    {
        $this->deleteImage();
    }

    public function deleteImage()
    {
        $path_ = Yii::getPathOfAlias('webroot') . '/' . $this->getImgPath() . '/';

        if(is_file($path = $path_ . $this->_old_image))
        {
            @unlink($path);
        }

        // Del thumb
        if(is_file($path = $path_ . $this->getThumbName($this->_old_image)))
        {
            @unlink($path);
        }
    }
}
