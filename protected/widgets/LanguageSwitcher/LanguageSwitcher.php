<?php
namespace app\widgets\LanguageSwitcher;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class LanguageSwitcher extends Widget
{
    /**
     * @var string CSS-класс контейнера
     */
    public $containerClass = 'language-switcher';

    /**
     * @var string CSS-класс активной ссылки
     */
    public $activeClass = 'active';

    /**
     * @var bool Показывать текущий язык в списке (сделать его ссылкой на самого себя)
     */
    public $showCurrent = true;

    public function run()
    {
        $languages = Yii::$app->params['languages'] ?? [];
        if (count($languages) < 2) {
            return '';
        }

        $current = Yii::$app->language;
        $items = [];

        foreach ($languages as $code => $label) {
            $isCurrent = ($code === $current);

            if ($isCurrent && !$this->showCurrent) {
                continue;
            }

            $items[] = Html::a(
                Html::encode($label),
                Url::to(['/site/language', 'lang' => $code]),
                [
                    'class' => $isCurrent ? $this->activeClass : '',
                    'hreflang' => $code,
                ]
            );
        }

        return Html::tag('div', implode('', $items), ['class' => $this->containerClass]);
    }
}