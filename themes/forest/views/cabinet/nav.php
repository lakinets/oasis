<?php
/**
 * @var yii\web\View $this
 */
use yii\helpers\Html;
use yii\helpers\Url;

// текущий маршрут для подсветки
$route = Yii::$app->controller->route;
?>
<div class="cabinet-menu">
    <ul>
        <?php
        $items = [
            ['label' => 'Главная',                'url' => ['/cabinet/default/index']],
            ['label' => 'Пополнить баланс',       'url' => ['/cabinet/deposit/index']],
            ['label' => 'Магазин',                'url' => ['/cabinet/shop/index']],
            ['label' => 'Услуги',                 'url' => ['/cabinet/services/index']],
            ['label' => 'Смена пароля',           'url' => ['/cabinet/change-password/index']],
            ['label' => 'Безопасность',           'url' => ['/cabinet/security/index']],
            ['label' => 'Персонажи',              'url' => ['/cabinet/characters/index']],
            ['label' => 'Поддержка',              'url' => ['/cabinet/tickets/index']],
            ['label' => 'Реферальная программа',  'url' => ['/cabinet/referals/index']],
            ['label' => 'Мои бонусы',             'url' => ['/cabinet/bonuses/index']],
            ['label' => 'Ввести бонус код',       'url' => ['/cabinet/bonuses/bonus-code']],
            ['label' => 'История пополнений',     'url' => ['/cabinet/transaction-history/index']],
            ['label' => 'История авторизаций',    'url' => ['/cabinet/auth-history/index']],
            ['label' => 'Личные сообщения',       'url' => ['/cabinet/messages/index']],
            ['label' => 'Выход',                  'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
        ];

        foreach ($items as $item):
            $active = trim(Url::to($item['url']), '/') === trim(Url::to([$route]), '/') ? 'active' : '';
            $opts   = array_merge(['class' => $active], $item['linkOptions'] ?? []);
        ?>
            <li class="<?= $active ?>">
                <?= Html::a($item['label'], $item['url'], $opts) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>