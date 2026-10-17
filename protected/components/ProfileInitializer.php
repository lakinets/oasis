<?php
namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\web\User;
use app\models\UserProfiles;

/**
 * Гарантирует наличие профиля у авторизованного пользователя.
 *
 * Подписывается на yii\web\User::EVENT_AFTER_LOGIN —
 * это событие компонента приложения Yii::$app->user, а не модели Users.
 *
 * Срабатывает при каждом успешном логине, включая авто-логин по cookie.
 */
class ProfileInitializer extends Component implements BootstrapInterface
{
    public function bootstrap($app)
    {
        /** @var User $user */
        $user = $app->user;

        $user->on(User::EVENT_AFTER_LOGIN, function ($event) {
            if ($event->identity && $event->identity->getId()) {
                UserProfiles::getOrCreate((int)$event->identity->getId());
            }
        });
    }
}