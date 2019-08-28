<?php

use App\Models\Partner;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Partner::class, function (Faker $faker) {
    return [
        'title' => $faker->company,
        'email' => $faker->email,
        'key' => Str::random(),
        'money_to_points_multiplier' => 1,
        'currency' => \HCurrency::CURRENCY_BIT,
        'default_language' => \HLanguage::LANGUAGE_RU,
        'default_country' => \HLanguage::LANGUAGE_RU,
        'balance' => 0,
        'partner_group_id' => \Registry::lazyPartnerGroupId(),
        'partner_group_role' => Partner::PARTNER_GROUP_ROLE_PARTNER,
    ];
});
