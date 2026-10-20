<?php
namespace app\widgets\LanguageSwitcher;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\AssetBundle;

class LanguageSwitcher extends Widget
{
    public $containerClass = 'language-switcher';
    public $activeClass = 'active';
    public $showCurrent = true;
    public $useFlags = false;
    public $flagDirectory = 'images';
    public $flagExtension = 'png';
    public $flagWidth = 24;
    public $flagHeight = 16;
    public $flagLinkStyle = 'display:inline-block; margin-right:6px; line-height:1;';
    public $flagImgStyle = 'display:inline-block; vertical-align:middle; border:1px solid rgba(0,0,0,.15);';

    public function run()
    {
        // Читаем доступные языки из настроек
        $availableRaw = \app\components\AppConfig::get('language.available', '["ru","en"]');
        $availableLangs = is_array($availableRaw) ? $availableRaw : json_decode($availableRaw, true);
        if (!is_array($availableLangs)) {
            $availableLangs = [];
        }

        // Если языков меньше 2, виджет вообще не показываем
        if (count($availableLangs) < 2) {
            return '';
        }

        // Формируем массив меток для выбранных языков
        $allLangs = ['ru' => 'Русский', 'en' => 'English', 'de' => 'Deutsch', 'fr' => 'Français'];
        $languages = [];
        foreach ($availableLangs as $code) {
            $languages[$code] = $allLangs[$code] ?? strtoupper($code);
        }

        $current = Yii::$app->language;
        $items   = [];
        $baseUrl = null;

        if ($this->useFlags) {
            /** @var LanguageSwitcherFlagsAsset $bundle */
            $bundle  = LanguageSwitcherFlagsAsset::register($this->view);
            $baseUrl = rtrim($bundle->baseUrl, '/') . '/' . trim($this->flagDirectory, '/');
        }

        foreach ($languages as $code => $label) {
            $isCurrent = ($code === $current);

            if ($isCurrent && !$this->showCurrent) {
                continue;
            }

            if ($this->useFlags) {
                $img = Html::img(
                    $baseUrl . '/' . $code . '.' . $this->flagExtension,
                    [
                        'alt'    => $label,
                        'title'  => $label,
                        'width'  => $this->flagWidth,
                        'height' => $this->flagHeight,
                        'style'  => $this->flagImgStyle,
                    ]
                );

                $items[] = Html::a(
                    $img,
                    Url::to(['/site/language', 'lang' => $code]),
                    [
                        'class'    => $isCurrent ? $this->activeClass : '',
                        'hreflang' => $code,
                        'style'    => $this->flagLinkStyle,
                        'encode'   => false,
                    ]
                );
            } else {
                $items[] = Html::a(
                    Html::encode($label),
                    Url::to(['/site/language', 'lang' => $code]),
                    [
                        'class'    => $isCurrent ? $this->activeClass : '',
                        'hreflang' => $code,
                    ]
                );
            }
        }

        return Html::tag('div', implode('', $items), ['class' => $this->containerClass]);
    }
}

class LanguageSwitcherFlagsAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/Languages/assets';
    public $css        = [];
    public $js         = [];
    public $depends    = [];
    public $publishOptions = ['forceCopy' => true];
}