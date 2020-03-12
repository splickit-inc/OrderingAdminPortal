<?php

$factory->defineAs(\App\Model\Merchant::class, 'MerchantWithRandomBrand', function (Faker\Generator $faker) {
    $brand = factory(\App\Model\Brand::class)->create();
    return [
        'numeric_id' => $faker->randomNumber,
        'alphanumeric_id' => $faker->randomElement,
        'rewardr_programs' => null,
        'shop_email' => $faker->email,
        'brand_id' => $brand->brand_id,
        'name' => $faker->company,
        'display_name' => $faker->company,
        'address1' => $faker->streetAddress,
        'address2' => $faker->secondaryAddress,
        'city' => $faker->city,
        'state' => $faker->stateAbbr,
        'zip' => $faker->postcode,
        'country' => 'US',
        'lat' => $faker->latitude,
        'lng' => $faker->longitude,
        'EIN_SS' => $faker->randomNumber(8),
        'description' => $faker->paragraph,
        'phone_no' => $faker->phoneNumber,
        'fax_no' => $faker->phoneNumber,
        'twitter_handle' => $faker->word,
        'time_zone' => -6,
        'cross_street' => $faker->word,
        'trans_fee_type' => $faker->randomElement(['F', 'P']),
        'trans_fee_rate' => 0,
        'show_tip' => $faker->randomElement(['Y', 'N']),
        'tip_minimum_percentage' => 10,
        'tip_minimum_trigger_amount' => 10
    ];
});