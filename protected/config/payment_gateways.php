<?php
/**
 * Метаданные платёжных шлюзов для админ-панели.
 * Сами ключи/секреты хранятся в таблице `config` (префиксы — ниже).
 * Единственная точка, где объявляется список шлюзов.
 */
return [
    'stripe' => [
        'name'            => 'Stripe',
        'name_en'         => 'Stripe',
        'description'     => 'Карты (Visa, Mastercard, Amex), Apple Pay, Google Pay. Глобально.',
        'class'           => 'app\\components\\payments\\StripeGateway',
        'enabled_param'   => 'payment.stripe.enabled',
        'test_param'      => 'stripe.test',
        'supports_test'   => true,
        'has_test'        => true,
        'param_prefixes'  => ['stripe.', 'payment.stripe.'],
        'region'          => 'global',
        'sort'            => 5,
    ],
    'robokassa' => [
        'name'            => 'Робокасса',
        'name_en'         => 'Robokassa',
        'description'     => 'Россия, СНГ: карты, кошельки, СБП',
        'class'           => 'app\\components\\payments\\RobokassaGateway',
        'enabled_param'   => 'payment.robokassa.enabled',
        'test_param'      => 'robokassa.test',
        'supports_test'   => true,
        'has_test'        => true,
        'param_prefixes'  => ['robokassa.', 'payment.robokassa.'],
        'region'          => 'cis',
        'sort'            => 10,
    ],
    'unitpay' => [
        'name'            => 'Unitpay',
        'name_en'         => 'Unitpay',
        'description'     => 'Россия, СНГ',
        'class'           => 'app\\components\\payments\\UnitpayGateway',
        'enabled_param'   => 'payment.unitpay.enabled',
        'test_param'      => 'unitpay.test',
        'supports_test'   => true, // Реализуем поддержку params[test]=1
        'has_test'        => true,
        'param_prefixes'  => ['unitpay.', 'payment.unitpay.'],
        'region'          => 'cis',
        'sort'            => 20,
    ],
    'nowpayments' => [
        'name'            => 'NOWPayments',
        'name_en'         => 'NOWPayments',
        'description'     => 'Криптовалюта, глобально',
        'class'           => 'app\\components\\payments\\NOWPaymentsGateway',
        'enabled_param'   => 'payment.nowpayments.enabled',
        'test_param'      => 'nowpayments.test',
        'supports_test'   => true, // Будет переключать URL на api.sandbox.nowpayments.io
        'has_test'        => true,
        'param_prefixes'  => ['nowpayments.', 'payment.nowpayments.'],
        'region'          => 'global',
        'sort'            => 30,
    ],
    'payop' => [
        'name'            => 'PayOp',
        'name_en'         => 'PayOp',
        'description'     => 'Агрегатор: карты, локальные методы ЕС/Азии/ЛатАм',
        'class'           => 'app\\components\\payments\\PayOpGateway',
        'enabled_param'   => 'payment.payop.enabled',
        'test_param'      => null,
        'supports_test'   => false,
        'has_test'        => false, // Провайдер официально не предоставляет песочницу
        'param_prefixes'  => ['payop.', 'payment.payop.'],
        'region'          => 'global',
        'sort'            => 40,
    ],
    'cryptomus' => [
        'name'            => 'Cryptomus',
        'name_en'         => 'Cryptomus',
        'description'     => 'Криптовалюта',
        'class'           => 'app\\components\\payments\\CryptomusGateway',
        'enabled_param'   => 'payment.cryptomus.enabled',
        'test_param'      => null,
        'supports_test'   => false,
        'has_test'        => false, // Провайдер не предоставляет песочницу
        'param_prefixes'  => ['cryptomus.', 'payment.cryptomus.'],
        'region'          => 'global',
        'sort'            => 50,
    ],
    'volet' => [
        'name'            => 'Volet.com',
        'name_en'         => 'Volet',
        'description'     => 'Международные карты, электронные кошельки',
        'class'           => 'app\\components\\payments\\VoletGateway',
        'enabled_param'   => 'payment.volet.enabled',
        'test_param'      => 'payment.volet.test',
        'supports_test'   => true, // Реализуем поддержку Sandbox
        'has_test'        => true,
        'param_prefixes'  => ['volet.', 'payment.volet.'],
        'region'          => 'global',
        'sort'            => 60,
    ],
    'interkassa' => [
        'name'            => 'Interkassa',
        'name_en'         => 'Interkassa',
        'description'     => 'СНГ, карты, кошельки',
        'class'           => 'app\\components\\payments\\InterkassaGateway',
        'enabled_param'   => 'payment.interkassa.enabled',
        'test_param'      => 'payment.interkassa.test',
        'supports_test'   => true, // Будет переключать на test_key и валюту XTS
        'has_test'        => true,
        'param_prefixes'  => ['interkassa.', 'payment.interkassa.'],
        'region'          => 'cis',
        'sort'            => 70,
    ],
];