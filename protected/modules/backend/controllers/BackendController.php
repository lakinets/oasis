<?php
namespace app\modules\backend\controllers;

use yii\web\Controller;

/**
 * Базовый контроллер для всего backend-модуля.
 * Задаёт общий layout и поведения.
 */
class BackendController extends Controller
{
    /** @var string */
    public $layout = 'master';   // <-- Указываем layout
}