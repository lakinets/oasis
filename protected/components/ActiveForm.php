<?php
namespace app\components;

use yii\bootstrap5\ActiveForm as BaseActiveForm;
use yii\helpers\Html;

/**
 * Кастомный ActiveForm для проекта.
 *
 * Отличия от yii\bootstrap5\ActiveForm:
 *  - errorSummary() рендерится как Bootstrap 5 alert с кнопкой закрытия.
 *
 * Использование:
 *   use app\components\ActiveForm;
 *   $form = ActiveForm::begin();
 */
class ActiveForm extends BaseActiveForm
{
    /**
     * @inheritDoc
     */
    public function errorSummary($models, $options = [])
    {
        Html::addCssClass($options, ['alert', 'alert-danger', 'alert-dismissible', 'fade', 'show']);
        $options['role'] = 'alert';

        $summary = parent::errorSummary($models, $options);

        if ($summary === '') {
            return '';
        }

        // Кнопка закрытия внутрь alert
        $closeBtn = Html::button('', [
            'type'            => 'button',
            'class'           => 'btn-close',
            'data-bs-dismiss' => 'alert',
            'aria-label'      => 'Close',
        ]);

        // Вставляем кнопку сразу после открывающего <div>
        return preg_replace(
            '/(<div[^>]*>)/',
            '$1' . $closeBtn,
            $summary,
            1
        );
    }
}