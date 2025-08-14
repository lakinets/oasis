<?php

namespace app\widgets\NewsSocials;

use yii\base\Widget;
use yii\helpers\Html;

class NewsSocialsWidget extends Widget
{
    public $params = [];
    public $charLimit = 300;

    public function run()
    {
        $this->registerAssets();
        echo $this->render('news-socials', [
            'params' => $this->params,
        ]);
    }

    private function registerAssets()
    {
        $view = $this->getView();
        $assetsUrl = \Yii::getAlias('@app/widgets/NewsSocials/libs/share42');

        $view->registerJsFile(
            $assetsUrl . '/share42.js',
            ['position' => \yii\web\View::POS_END]
        );
    }
}