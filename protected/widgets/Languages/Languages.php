<?php
namespace app\widgets\Languages;

use Yii;
use yii\base\Widget;

class Languages extends Widget
{
    public function run()
    {
        $availableRaw = \app\components\AppConfig::get('language.available', '["ru","en"]');
        $availableLangs = is_array($availableRaw) ? $availableRaw : json_decode($availableRaw, true);
        if (!is_array($availableLangs)) {
            $availableLangs = [];
        }

        if (count($availableLangs) < 2) {
            return '';
        }

        $allLangs = ['ru' => 'RU', 'en' => 'EN', 'de' => 'DE', 'fr' => 'FR'];
        $languages = [];
        foreach ($availableLangs as $code) {
            $languages[$code] = $allLangs[$code] ?? strtoupper($code);
        }

        return $this->render('index', [
            'languages' => $languages,
            'current'   => Yii::$app->language,
        ]);
    }
}