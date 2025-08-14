<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Cookie;

class UserNotifications extends Widget
{
    private $_cookieName = 'messages';

    public function run()
    {
        $user = Yii::$app->user;
        $request = Yii::$app->request;
        $response = Yii::$app->response;

        if ($user->isGuest) {
            return '';
        }

        // Подключение стилей
        $assetsPath = Yii::getAlias('@app/widgets/UserNotifications/css');
        $assetsUrl = Yii::$app->assetManager->publish($assetsPath)[1];
        Yii::$app->view->registerCssFile($assetsUrl . '/style.css');

        $countMessages = $this->getCountMessages();
        $cookieCountMessages = (int) ($request->cookies->getValue($this->_cookieName, 0));

        if ($countMessages > $cookieCountMessages) {
            $count = $countMessages - $cookieCountMessages;

            // Обновляем куку
            $response->cookies->add(new Cookie([
                'name' => $this->_cookieName,
                'value' => $countMessages,
                'expire' => time() + 3600 * 24 * 365,
            ]));

            // Перевод с числом
            $countMessagesTranslate = Yii::t('main', 'новое сообщение|новых сообщения|новых сообщений|новых сообщения', $count);

            return Html::tag('div',
                Html::a(
                    Yii::t('main', 'У Вас <b>{count}</b> {count_text}', [
                        'count' => $count,
                        'count_text' => $countMessagesTranslate,
                    ]),
                    Url::to(['/cabinet/messages/index'])
                ),
                ['class' => 'user-messages-block']
            );
        }

        return '';
    }

    private function getCountMessages(): int
    {
        return (int) Yii::$app->db->createCommand("
            SELECT COUNT(*) 
            FROM {{%user_messages}} 
            WHERE user_id = :user_id AND status = :status
        ")
        ->bindValue(':user_id', Yii::$app->user->id)
        ->bindValue(':status', 1) // ActiveRecord::STATUS_ON предполагается == 1
        ->queryScalar();
    }
}
