<?php

use App\Models\Action;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Action::class, function (Faker $faker) {
    return [
        'type' => Action::TYPE_SIGNUP,
        'value' => 5,
        'value_type' => Action::VALUE_TYPE_FIXED,
        'partner_id' => \Registry::lazyPartnerId(),
        'status' => Action::STATUS_ENABLED,
        'is_system' => false,
    ];
});

$factory->defineAs(Action::class, Action::TYPE_SIGNUP, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_SIGNUP,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_CUSTOM_BONUS, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_CUSTOM_BONUS,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_JOIN_FB, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_JOIN_FB,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_JOIN_VK, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_JOIN_VK,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_SHARE_FB, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_SHARE_FB,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_SHARE_VK, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_SHARE_VK,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_REFILL_BIT, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_REFILL_BIT,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_EXCHANGE_ETH_TO_BIT, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_EXCHANGE_ETH_TO_BIT,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_AFFILIATE_ACTION_ADMITAD, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_AFFILIATE_ACTION_ADMITAD,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_ORDER_CASHBACK, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_ORDER_CASHBACK,
    ]);
});

$factory->defineAs(Action::class, Action::TYPE_ORDER_REFERRAL, function () {
    return factory(Action::class)->raw([
        'type' => Action::TYPE_ORDER_REFERRAL,
        'value_type' => Action::VALUE_TYPE_PERCENT,
        'config' => [
            'referral-reward-value' => 5,
            'referral-reward-lifetime' => 604800,
            'referral-reward-value-type' => 'percent',
        ],
    ]);
});

$factory->state(Action::class, 'disabled', [
    'status' => Action::STATUS_DISABLED,
]);
