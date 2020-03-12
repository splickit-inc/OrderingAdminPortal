<?php

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS',
        'remember_token' => str_random(10),
        'visibility' => $faker->randomElement(['global', 'mine_only', 'operator', 'all', 'brand']),
        'organization_id' => 1
    ];
});

$factory->defineAs(App\User::class, 'global', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS',
        'remember_token' => str_random(10),
        'visibility' => 'global',
        'organization_id' => 1
    ];
});

$factory->defineAs(App\User::class, 'mine_only', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS',
        'remember_token' => str_random(10),
        'visibility' => 'mine_only',
        'organization_id' => 1
    ];
});

$factory->defineAs(App\User::class, 'operator', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS',
        'remember_token' => str_random(10),
        'visibility' => 'operator',
        'organization_id' => 1
    ];
});

$factory->defineAs(App\User::class, 'all', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS',
        'remember_token' => str_random(10),
        'visibility' => 'all',
        'organization_id' => 1
    ];
});

$factory->defineAs(App\User::class, 'brand', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '$2y$10$/jsqkCYmOuFaqqObnqy1tOBEWe5b.evu5NpfthbC1TKrmEeiA6FUS',
        'remember_token' => str_random(10),
        'visibility' => 'brand',
        'organization_id' => 1
    ];
});
