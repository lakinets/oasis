<?php
namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\web\Cookie;

class LanguageSelector extends Component implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Читаем настройки из админки через AppConfig (он умеет парсить JSON)
        $availableRaw = \app\components\AppConfig::get('language.available', '["ru","en"]');
        $availableLangs = is_array($availableRaw) ? $availableRaw : json_decode($availableRaw, true);
        if (!is_array($availableLangs)) {
            $availableLangs = [];
        }

        $defaultLang = \app\components\AppConfig::get('language.default', 'ru');
        if (!in_array($defaultLang, $availableLangs, true) && !empty($availableLangs)) {
            $defaultLang = reset($availableLangs);
        }

        // ПРАВИЛО 1: Если выбрано 0 языков -> всегда RU (язык-источник)
        if (count($availableLangs) === 0) {
            $app->language = 'ru';
            $app->formatter->locale = 'ru';
            return;
        }

        // ПРАВИЛО 2: Если выбран 1 язык -> всегда принудительно только он (игнорируем куки)
        if (count($availableLangs) === 1) {
            $app->language = reset($availableLangs);
            $app->formatter->locale = $app->language;
            return;
        }

        // ПРАВИЛО 3: Если 2 и более языков -> стандартная логика
        // 1. Язык из URL (?lang=en)
        $fromUrl = $app->request->get('lang');
        if ($fromUrl && in_array($fromUrl, $availableLangs, true)) {
            $app->session->set('language', $fromUrl);
            $app->response->cookies->add(new Cookie([
                'name'   => 'language',
                'value'  => $fromUrl,
                'expire' => time() + 60 * 60 * 24 * 365,
            ]));
            $app->language = $fromUrl;
            $app->formatter->locale = $fromUrl;
            return;
        }

        // 2. Язык из cookie
        $fromCookie = $app->request->cookies->getValue('language');
        if ($fromCookie && in_array($fromCookie, $availableLangs, true)) {
            $app->language = $fromCookie;
            $app->formatter->locale = $fromCookie;
            $app->session->set('language', $fromCookie);
            return;
        }

        // 3. Язык из сессии
        $fromSession = $app->session->get('language');
        if ($fromSession && in_array($fromSession, $availableLangs, true)) {
            $app->language = $fromSession;
            $app->formatter->locale = $fromSession;
            return;
        }

        // 4. Язык браузера
        $fromBrowser = $app->request->getPreferredLanguage($availableLangs);
        if ($fromBrowser && in_array($fromBrowser, $availableLangs, true)) {
            $app->language = $fromBrowser;
            $app->formatter->locale = $fromBrowser;
            return;
        }

        // 5. Дефолт из админки
        $app->language = $defaultLang;
        $app->formatter->locale = $defaultLang;
    }
}