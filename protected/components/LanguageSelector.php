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
        $supported = array_keys($app->params['languages'] ?? []);
        if (!$supported) {
            return;
        }

        $default = $app->params['defaultLanguage'] ?? $supported[0];

        // 1. Язык из URL (?lang=en)
        $fromUrl = $app->request->get('lang');
        if ($fromUrl && in_array($fromUrl, $supported, true)) {
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
        if ($fromCookie && in_array($fromCookie, $supported, true)) {
            $app->language = $fromCookie;
            $app->formatter->locale = $fromCookie;
            $app->session->set('language', $fromCookie);
            return;
        }

        // 3. Язык из сессии
        $fromSession = $app->session->get('language');
        if ($fromSession && in_array($fromSession, $supported, true)) {
            $app->language = $fromSession;
            $app->formatter->locale = $fromSession;
            return;
        }

        // 4. Язык браузера
        $fromBrowser = $app->request->getPreferredLanguage($supported);
        if ($fromBrowser && in_array($fromBrowser, $supported, true)) {
            $app->language = $fromBrowser;
            $app->formatter->locale = $fromBrowser;
            return;
        }

        // 5. Дефолт
        $app->language = $default;
        $app->formatter->locale = $default;
    }
}