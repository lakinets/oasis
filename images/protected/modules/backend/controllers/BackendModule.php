<?php

class BackendModule extends CWebModule
{
    public function init()
    {
        // Выполняем инициализацию модуля
        parent::init();

        // Регистрация翻译 для модуля
        Yii::setPathOfAlias('backend', dirname(__FILE__));

        // Регистрация компонентов и других настроек
        // $this->setComponents(array(
        //     // ...
        // ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // Выполняем проверку доступа или другие действия перед выполнением действия
            // Например, проверка авторизации пользователя
            if (!Yii::app()->user->isGuest) {
                return true;
            } else {
                $this->redirect(array('login'));
            }
        }
        return false;
    }
}