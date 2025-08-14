<?php
use yii\helpers\Html;

echo Html::tag('div', '', [
    'class' => 'share42init',
    'data-description' => $params['data-description'] ?? '',
    'data-title' => $params['data-title'] ?? '',
    'data-url' => $params['data-url'] ?? '',
    'data-image' => $params['data-image'] ?? '',
]);