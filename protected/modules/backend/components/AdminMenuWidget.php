<?php
namespace app\modules\backend\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class AdminMenuWidget extends Widget
{
    public function run()
    {
        return $this->render('_admin_menu');
    }
}