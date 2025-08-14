<?php

namespace app\widgets\FlashMessages;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class FlashMessages extends Widget
{
    public $cssClassPrefix = 'alert alert-';

    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $output = '';

        foreach ($flashes as $type => $data) {
            if (!is_array($data)) {
                $data = [$data];
            }
            foreach ($data as $message) {
                $output .= Html::tag(
                    'div',
                    Html::encode($message),
                    ['class' => $this->cssClassPrefix . $type]
                );
            }
            $session->removeFlash($type);
        }

        return $output;
    }
}
