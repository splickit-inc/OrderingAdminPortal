<?php

$factory->define(App\Model\Brand::class, function (Faker\Generator $faker) {
    $brand_name = $faker->company;
    return [
        'brand_name' => $brand_name,
        'brand_external_identifier' => 'com.yourbiz.' . $brand_name,
        'active' => 'Y',
        'gift_enabled' => 'Y',
        'contact_number_required' => $faker->phoneNumber,
        'allows_tipping' => 'Y',
        'allows_pricing_adjustments' => 'Y',
        'allows_in_store_payments' => 'N',
        'logical_delete' => 'N',
        'support_email' => $faker->email,
        'production' => 'Y'
    ];
});