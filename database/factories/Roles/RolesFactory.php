<?php

$factory->defineAs(App\Model\Role::class, 'super_user', function (Faker\Generator $faker) {
    return [
        'id' => '1',
        'name' => 'Order 140 Super User',
        'description' => 'Carys Team, Kendalls Team, Order 140 Ops'
    ];
});

$factory->defineAs(App\Model\Role::class, 'partner_admin', function (Faker\Generator $faker) {
    return [
        'id' => '2',
        'name' => 'Partner Admin',
        'description' => 'Epson'
    ];
});

$factory->defineAs(App\Model\Role::class, 'reseller', function (Faker\Generator $faker) {
    return [
        'id' => '3',
        'name' => 'Reseller Account Manager',
        'description' => 'Epson VAR'
    ];
});

$factory->defineAs(App\Model\Role::class, 'owner_operator', function (Faker\Generator $faker) {
    return [
        'id' => '5',
        'name' => 'Store Owner Operator',
        'description' => 'Lindsay at Lindsays Deli'
    ];
});

$factory->defineAs(App\Model\Role::class, 'store_manager', function (Faker\Generator $faker) {
    return [
        'id' => '6',
        'name' => 'Store Manager',
        'description' => 'A manager working under a store owner/operator, with limited permissions'
    ];
});

$factory->defineAs(App\Model\Role::class, 'multi_operator', function (Faker\Generator $faker) {
    return [
        'id' => '7',
        'name' => 'Multi-Location Owner Operator',
        'description' => 'This is an operator that manages multiple locations'
    ];
});

$factory->defineAs(App\Model\Role::class, 'brand_manager', function (Faker\Generator $faker) {
    return [
        'id' => '8',
        'name' => 'Brand Manager',
        'description' => 'Arielle at Goodcents'
    ];
});

$factory->defineAs(App\Model\Role::class, 'store_associate', function (Faker\Generator $faker) {
    return [
        'id' => '9',
        'name' => 'Store Associate',
        'description' => 'A merchant employee who only managers orders'
    ];
});